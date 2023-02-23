<?php

namespace App\Admin\Services;

use App\Admin\Admin;
use App\Admin\Helpers\LogHelper;
use App\Admin\Models\Imports\ImportStaff;
use App\Admin\Repositories\SchoolClassRepository;
use App\Admin\Repositories\StaffRepository;
use App\Models\ClassSubject;
use App\Models\School;
use App\Models\SchoolStaff;
use App\Models\StaffLinkingSchool;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class StaffService
{
    protected $staffRepo;
    protected $classRepo;

    public function __construct(StaffRepository $repo, SchoolClassRepository $classRepo)
    {
        $this->staffRepo = $repo;
        $this->classRepo = $classRepo;
    }

    public function findByCode($staffCode)
    {
        return $this->staffRepo->findByCode($staffCode);
    }

    public function findById($staffId)
    {
        return $this->staffRepo->findById($staffId, ['*'], ['staffSubjects', 'staffSubjects.subject', 'subjects', 'staffGrades', 'teacherPlans', 'manageGroup', 'manageGroup.regularGroup.staffs']);
    }

    public function index()
    {
        return ['staffs' => $this->staffRepo->all(['*'], ['staffGrades', 'subjects'])];
    }

    public function findBySchool($schoolId)
    {
        return $this->staffRepo->findBySchoolId($schoolId);
    }

    public function allBySchool($schoolId)
    {
        return [
            'school' => School::find($schoolId),
            'staffs' => $this->staffRepo->findBySchoolId($schoolId)
        ];
    }

    public function validate($params)
    {
        $validator = ImportStaff::singleValidator($params);
        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Lỗi nhập liệu',
                'validator' => $validator
            ];
        }
        return true;
    }

    public function create($params, $schoolId)
    {
        //$validator = $this->validate($params);
        //if($validator !== true ) return $validator;
        /** @var $school School */
        $school = School::with('branches')->find($schoolId);
        if (is_null($school)) {
            return redirect()->back()->with('error', 'Dữ liệu không đúng. Vui lòng kiểm tra lại!');
        }
        DB::beginTransaction();
        try {
            $subjects = $grades = [];

            if (isset($params['subjects'])) {
                $subjects = $params['subjects'];
                unset($params['subjects']);
            }

            if (isset($params['grades'])) {
                $grades = $params['grades'];
                unset($params['grades']);
            }

            $currentExist = $school->getLastestStaffCode();
            $no = $currentExist + 1;
            $staffCode = $school->generateStaffCode($no);

            //Render Data and create
            $params['staff_code'] = $staffCode;
            $params['school_id'] = $school->id;

            /** @var $staff SchoolStaff */
            $staff = $this->staffRepo->create($params);

            /* Create Account staff */
            if ($staff->canCreateAccount()) {
                $staff->createAccount($school, 'Tuyến TTYT Học Đường - ' . $school['school_name'] . ' - ' . $staff['fullname']);
            }

            $this->staffRepo->setSubjects($staff->id, $subjects);
            $this->staffRepo->setGrades($staff->id, $grades);

            $activity = 'Thêm mới nhân viên tại trường: "' . $staff['fullname'] . '"';
            DB::commit();

            return ['success' => true, 'message' => 'Tạo nhân viên thành công'];
        } catch (Exception $ex) {
            DB::rollBack();
            if (env('APP_ENV') !== 'production') dd($ex);
            Log::error($ex->getMessage(), [
                'process' => '[create subject]',
                'function' => __function__,
                'file' => basename(__FILE__),
                'line' => __line__,
                'path' => __file__,
                'error_message' => $ex->getMessage()
            ]);
            return ['success' => false, 'message' =>  $ex->getMessage()];
        }
    }

    public function updateLinkingSchools($params)
    {
        if ($params->type == 1) { // add linking school
            $dataReturn['staff_id'] = $params->staffId;
            $dataReturn['school'] = [];
            if (!empty($params->schoolId)) {
                // trường hợp xóa bớt school
                // xóa tất cả những StaffLinkingSchool ko có trong array id school truyền lên
                $lstAdditionalSchoolId = [];
                foreach ($params->schoolId as $checkId) {
                    $lstAdditionalSchoolId[] = $checkId;
                }
                StaffLinkingSchool::where('staff_id', $params->staffId)->whereNotIn('additional_school_id', $lstAdditionalSchoolId)->delete();
                foreach ($params->schoolId as $key => $additionalSchoolId) {
                    if (empty($additionalSchoolId)) {
                        continue;
                    }
                    // set data đầu tiên
                    $dataReturn['school'][$key]['id'] = $additionalSchoolId;
                    $dataReturn['school'][$key]['name'] = School::find($additionalSchoolId)->school_name;
                    // check nếu đã có additional_school_id với staff này thì bỏ qua
                    $checkStaffLinkingSchool = StaffLinkingSchool::where('staff_id', $params->staffId)->where('additional_school_id', $additionalSchoolId)->first();
                    if (isset($checkStaffLinkingSchool) && !empty($checkStaffLinkingSchool)) {
                        $dataReturn['school'][$key]['working_day'] = $checkStaffLinkingSchool['working_days'];
                        continue;
                    }
                    // nếu chưa có thì insert mới vào
                    StaffLinkingSchool::create([
                        'staff_id' => $params->staffId,
                        'primary_school_id' => SchoolStaff::find($params->staffId)->school_id,
                        'additional_school_id' => $additionalSchoolId,
                        'working_days' => json_encode([])
                    ]);
                    $dataReturn['school'][$key]['working_day'] = json_encode([]);
                }
                $dataReturn['error'] = 0;
                return $dataReturn;
            } else {
                StaffLinkingSchool::where('staff_id', $params->staffId)->delete();
            }
            $dataReturn['error'] = 0;
            return $dataReturn;
        } else { // add config staff
            if (!empty($params->schoolId)) {
                $dataReturn = $this->updateLinkingStaff($params->staffId, $params->schoolId);
                $dataReturn['error']=0;
                return $dataReturn;
            }
            $dataReturn['error']=1;
            return $dataReturn;
        }



        if(isset($params['staffs'])) {
            foreach($params['staffs'] as $staff) {
                if(isset($staff['linkingSchools'])) {
                    $this->updateLinkingStaff($staff['id'], $staff['linkingSchools'], $staff['workingDays']);
                }
            }
        }
    }

    public function update($staffId, $params)
    {
        //$validator = $this->validate($params);
        //if($validator !== true ) return $validator;

        /** @var $staff SchoolStaff */
        $staff = SchoolStaff::with('school')->find($staffId);
        if (is_null($staff)) {
            return redirect()->back()->with('error', 'Dữ liệu không đúng. Vui lòng kiểm tra lại!');
        }

        DB::beginTransaction();
        try {
            $subjects = $grades = [];

            if (isset($params['subjects'])) {
                $subjects = $params['subjects'];
                unset($params['subjects']);
            }

            if (isset($params['grades'])) {
                $grades = $params['grades'];
                unset($params['grades']);
            }

            $old_staff = $staff->replicate();
            $staff->update($params);
            $changes = $staff->getChanges();
            unset($changes['updated_at']);
            $activity = 'Chỉnh sửa thông tin nhân viên: ' . $staff->fullname . '"';
            $data_activity = $activity . '<br>';
            if (isset($changes['fullname'])) $data_activity .= ' -  Tên: "' . $old_staff->fullname . '" -> "' . $staff->fullname . '"<br>';
            if (isset($changes['dob'])) $data_activity .= ' -  Ngày sinh: "' . $old_staff->dob . '" -> "' . $staff->dob . '"<br>';
            if (isset($changes['gender'])) $data_activity .= ' -  Giới tính: "' . $old_staff->gender . '" -> "' . $staff->gender . '"<br>';
            if (isset($changes['ethnic'])) $data_activity .= ' -  Dân tộc: "' . $old_staff->ethnic . '" -> "' . $staff->ethnic . '"<br>';
            if (isset($changes['religion'])) $data_activity .= ' -  Tôn giáo: "' . $old_staff->religion . '" -> "' . $staff->religion . '"<br>';
            if (isset($changes['nationality'])) $data_activity .= ' -  Quốc tịch: "' . $old_staff->nationality . '" -> "' . $staff->nationality . '"<br>';
            if (isset($changes['address'])) $data_activity .= ' -  Địa chỉ: "' . $old_staff->address . '" -> "' . $staff->address . '"<br>';
            if (isset($changes['identity_card'])) $data_activity .= ' -  Chứng minh nhân dân: "' . $old_staff->identity_card . '" -> "' . $staff->identity_card . '"<br>';
            if (isset($changes['phone_number'])) $data_activity .= ' -  Số điện thoại: "' . $old_staff->phone_number . '" -> "' . $staff->phone_number . '"<br>';
            if (isset($changes['email'])) $data_activity .= ' -  Email: "' . $old_staff->email . '" -> "' . $staff->email . '"<br>';
            if (isset($changes['qualification'])) $data_activity .= ' -  Trình độ chuyên môn: "' . $old_staff->qualification . '" -> "' . $staff->qualification . '"<br>';
            if (isset($changes['position'])) $data_activity .= ' -  Vị trí: "' . $old_staff->position . '" -> "' . $staff->position . '"<br>';
            if (isset($changes['school_branch_id'])) $data_activity .= ' -  Điểm trường: "' . collect($staff->class->school->branches)->where('id', intval($old_staff->school_branch_id))->first()['branch_name'] . '" -> "' . collect($staff->class->school->branches)->where('id', intval($staff->school_branch_id))->first()['branch_name'] . '"<br>';
            if (isset($changes['status'])) $data_activity .= ' -  Trạng thái làm việc: "' . $old_staff->status . '" -> "' . $staff->status . '"<br>';
            $old_staff_responsible = $old_staff->responsible ? 'Có' : 'Không';
            $new_staff_responsible = $staff->responsible ? 'Có' : 'Không';
            $old_staff_professional_certificate = $old_staff->professional_certificate ? 'Có' : 'Không';
            $new_staff_professional_certificate = $staff->professional_certificate ? 'Có' : 'Không';
            if (isset($changes['responsible'])) $data_activity .= ' -  Chuyên trách: "' . $old_staff_responsible . '" -> "' . $new_staff_responsible . '"<br>';
            if (isset($changes['professional_certificate'])) $data_activity .= ' - Chứng chỉ hành nghề: "' . $old_staff_professional_certificate . '" -> "' . $new_staff_professional_certificate . '"<br>';


            $this->staffRepo->setSubjects($staff->id, $subjects);
            $this->staffRepo->setGrades($staff->id, $grades);

            LogHelper::saveActivityLog($activity, $staff->school_id, $staff->school_branch_id, $data_activity);
            DB::commit();

            return ['success' => true, 'message' => 'Sửa nhân viên thành công'];
        } catch (Exception $ex) {
            DB::rollBack();
            if (env('APP_ENV') !== 'production') dd($ex);
            Log::error($ex->getMessage(), [
                'process' => '[create subject]',
                'function' => __function__,
                'file' => basename(__FILE__),
                'line' => __line__,
                'path' => __file__,
                'error_message' => $ex->getMessage()
            ]);
            return ['success' => false, 'message' =>  $ex->getMessage()];
        }
    }

    public function updateSubjects($staffId, $subjects)
    {
        $this->staffRepo->setSubjects($staffId, $subjects);
    }

    public function updateLinkingStaff($staffId, $linkingSchools)
    {
        return $this->staffRepo->setLinkingStaff($staffId, $linkingSchools);
    }

    public function updateGrades($staffId, $grades)
    {
        $this->staffRepo->setGrades($staffId, $grades);
    }

    public function checkTeacherLesson($schoolId, $classId, $classSubjects, $maximumLessons)
    {
        DB::beginTransaction();
        $listStaff = [];
        foreach ($classSubjects as $classSubject) {
            if (!empty($classSubject['staff_id']) && !in_array($classSubject['staff_id'], $listStaff)) {
                array_push($listStaff, $classSubject['staff_id']);
            }
            if ($classSubject['staff_id'] !== null) {
                ClassSubject::find($classSubject['id'])->update($classSubject);
            }
        }
        foreach ($listStaff as $staffId) {
            $staff = $this->staffRepo->findById($staffId, ['*'], ['assignedClassSubject']);
            $totalLesson = 0;
            if (count($staff->assignedClassSubject) > 0) {
                foreach ($staff->assignedClassSubject as $classSubject) {
                    $totalLesson += $classSubject->lesson_per_week;
                }
            }
            if ($totalLesson > $maximumLessons) return ['success' => false, 'message' => "Tổng số tiết học cho giáo viên {$staff->fullname} đã quá 28 tiết"];
        }
        DB::rollBack();
        return ['success' => true, 'message' => 'Dữ liệu hợp lệ'];
    }

    public function findClassId($staffId)
    {
        return  ClassSubject::where('staff_id', $staffId)->get();
    }
}
