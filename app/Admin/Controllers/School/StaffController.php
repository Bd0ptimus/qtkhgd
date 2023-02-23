<?php

namespace App\Admin\Controllers\School;

use App\Admin\Exports\ScheduleExport;
use App\Admin\Models\Exports\ExportSchoolStaffs;
use App\Admin\Models\Imports\ImportStaff;
use App\Admin\Services\StaffService;
use App\Admin\Services\CommonService;
use App\Admin\Services\RegularGroupService;
use App\Admin\Services\SchoolService;
use App\Admin\Services\SubjectService;
use App\Admin\Services\TimetableService;
use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolBranch;
use App\Models\SchoolStaff;
use App\Models\StaffLinkingSchool;
use App\Models\Subject;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;


class StaffController extends Controller
{

    protected $staffService;
    protected $commonService;
    protected $subjectService;
    protected $timetableService;
    protected $schoolService;
    protected $rgService;

    public function __construct(
        StaffService $service, 
        CommonService $commonService, 
        SubjectService $subjectService,
        TimetableService $timetableService,
        SchoolService $schoolService,
        RegularGroupService $rgService
    )
    {
        $this->staffService = $service;
        $this->commonService = $commonService;
        $this->subjectService = $subjectService;
        $this->timetableService = $timetableService;
        $this->schoolService = $schoolService;
        $this->rgService = $rgService;
    }

    public function view($id)
    {
        $staff = SchoolStaff::where('id', $id)->with('school')->first();
        $school = $staff->school;
        $title = 'Thông tin nhân viên';
        $breadcrumbs = [
            ['name' => 'Danh sách các đơn vị trường học', 'link' => route('school.index')],
            ['name' => $school->school_name, 'link' => route('admin.school.manage', ['id' => $school->id])],
            ['name' => 'Danh sách nhân viên theo trường', 'link' => route('admin.school.view_staff_list', ['id' => $school->id])],
            ['name' => $title],
        ];
        $activity = 'Xem thông tin nhân viên: "' . $staff->fullname . '"';
        $this->saveActivityLog($activity, $school->id);
        return $this->renderView('admin.school.staff.view', [
            'staff' => $staff,
            'title' => $title,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    public function viewStaffList($id, Request $request)
    {
        if ($request->isMethod('post')) {
            $staff = SchoolStaff::find($request->staff_id);
            $staff->assignToClass($request->class_id);
            return redirect()->back()->with('success', 'Tạo tài khoản và giao lớp thành công!');
        }
        $position = request()->query('position', null);
        $school = School::with(['staffs' => function ($query) use ($position) {
            if (!is_null($position)) {
                $query->where('position', $position);
            }
        }, 'staffs.schoolBranch', 'branches', 'classes'])->find($id);

        if (is_null($school)) {
            return redirect()->back()->with('error', 'Dữ liệu không đúng. Vui lòng kiểm tra lại!');
        }

        $title = 'Danh sách nhân viên theo trường';
        $breadcrumbs = [
            ['name' => 'Danh sách các đơn vị trường học', 'link' => route('school.index')],
            ['name' => $school->school_name, 'link' => route('admin.school.manage', ['id' => $school->id])],
            ['name' => $title],
        ];

        $data = [
            'position' => SchoolStaff::POSITIONS
        ];
        $activity = "Xem trang Danh sách nhân viên tại trường";
        $this->saveActivityLog($activity, $school->id);
        return $this->renderView('admin.school.staff.staff_list', [
            'school' => $school,
            'position' => $position,
            'title' => $title,
            'breadcrumbs' => $breadcrumbs,
            'data' => $data,
        ]);
    }

    public function linkingStaff(Request $request, $id) {
        $school = School::find($id);

        if ($request->isMethod('post')) {
            $dataReturn = $this->staffService->updateLinkingSchools($request);
            return response()->json($dataReturn);
            //return redirect()->back()->with('success', 'Tạo tài khoản và giao lớp thành công!');
        }
        Log::info("after : ");
        $linkingStaffs = SchoolStaff::where([
            'school_id' => $id,
            'is_linking_staff' => 1
        ])->with('staffGrades', 'staffSubjects', 'linkingSchools')->get();

        $districtSchools = School::where('district_id', $school->district_id)->where('id', '!=', $id)->get();

        $title = 'Danh sách nhân viên theo trường';
        $breadcrumbs = [
            ['name' => 'Quản lý trường', 'link' => route('admin.school.manage', ['id' => $id])],
            ['name' => $title],
        ];

        return $this->renderView('admin.school.staff.linking_staff', [
            'linkingStaffs' => $linkingStaffs,
            'districtSchools' => $districtSchools,
            'breadcrumbs'=> $breadcrumbs,
            'title' => $title,
            'subjects' => Subject::all(),
            'grades' => $this->commonService->getGradeBySchoolType($school->school_type),
            'arraySlots' => StaffLinkingSchool::SLOT_BY_DAY
        ]);
    }


     

    public function exportStaffs($id, Request $request)
    {
        $position = $request->query('position', null);
        $school = School::with(['staffs' => function ($query) use ($position) {
            if (!is_null($position)) {
                $query->where('position', $position);
            }
        }, 'staffs.schoolBranch', 'branches', 'classes'])->find($id);

        if (is_null($school)) {
            return redirect()->back()->with('error', 'Dữ liệu không đúng. Vui lòng kiểm tra lại!');
        }

        return (new ExportSchoolStaffs($school))->download('ds_nhan_vien.xls');
    }

    public function assignBranch()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => 'Method not allow!']);
        } else {
            $branchId = request('branchId');
            $selectedStaffIds = request('selectedStaffIds');
            $branch = SchoolBranch::find($branchId);

            if (is_null($branch)) {
                return response()->json(['error' => 1, 'msg' => 'Branch not found']);
            }

            SchoolStaff::whereIn('id', $selectedStaffIds)->update(['school_branch_id' => $branchId]);

            return response()->json(['error' => 0, 'msg' => '']);
        }
    }

    public function addStaff($id)
    {
        $school = School::with('branches')->find($id);
        if (is_null($school)) {
            return redirect()->back()->with('error', 'Dữ liệu không đúng. Vui lòng kiểm tra lại!');
        }
        // fitler by shool type
        $grades = $this->commonService->getGradeBySchoolType($school->school_type);

        $data = [
            'gender' => SchoolStaff::GENDER,
            'ethnic' => SchoolStaff::ETHNICS,
            'religion' => SchoolStaff::RELIGIONS,
            'nationality' => SchoolStaff::NATIONALITIES,
            'qualification' => SchoolStaff::QUALIFICATIONS,
            'position' => SchoolStaff::POSITIONS,
            'status' => SchoolStaff::STATUS,
        ];

        return $this->renderView('admin.school.staff.form_staff', [
            'title' => 'Thêm nhân viên cho trường',
            'routing' => route('admin.school.post_add_staff', ['id' => $school->id]),
            'school' => $school,
            'data' => $data,
            'subjects' => Subject::where('school_id', $id)->get(),
            'grades' => $grades
        ]);
    }

    public function postAddStaff($id, Request $request)
    {
        $request->validate([
            'fullname' => 'required',
            'email' => 'nullable|email',
            'address' => 'required',
            'dob' => 'required',
        ], [
            'fullname.required' => __('validation.required', ['attribute' => 'tên đầy đủ']),
            'email.email' => __('validation.email'),
            'address.required' => __('validation.required', ['attribute' => 'địa chỉ']),
            'dob.required' => __('validation.required', ['attribute' => 'ngày sinh']),
        ]);

        $data = request()->only([
            'fullname',
            'dob',
            'gender',
            'ethnic',
            'religion',
            'nationality',
            'address',
            'identity_card',
            'phone_number',
            'email',
            'qualification',
            'position',
            'school_branch_id',
            'status',
            'responsible',
            'professional_certificate',
            'subjects',
            'grades'
        ]);

        $result = $this->staffService->create($data, $id);

        //if(isset($result['validator']))  return redirect()->back()->withErrors($result['validator'])->withInput();

        return redirect()->route('admin.school.view_staff_list', [
            'id' => $id
        ])->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function editStaff($id)
    {
        $staff = SchoolStaff::with(['school.branches', 'staffGrades', 'staffSubjects'])->find($id);
        if (is_null($staff)) {
            return redirect()->back()->with('error', 'Dữ liệu không đúng. Vui lòng kiểm tra lại!');
        }
        // fitler by shool type
        $grades = $this->commonService->getGradeBySchoolType($staff->school->school_type);

        $data = [
            'gender' => SchoolStaff::GENDER,
            'ethnic' => SchoolStaff::ETHNICS,
            'religion' => SchoolStaff::RELIGIONS,
            'nationality' => SchoolStaff::NATIONALITIES,
            'qualification' => SchoolStaff::QUALIFICATIONS,
            'position' => SchoolStaff::POSITIONS,
            'status' => SchoolStaff::STATUS,
        ];

        return $this->renderView('admin.school.staff.form_staff', [
            'title' => 'Chỉnh sửa nhân viên cho trường',
            'routing' => route('admin.school.post_edit_staff', ['id' => $staff->id]),
            'school' => $staff->school,
            'staff' => $staff,
            'data' => $data,
            'subjects' => $staff->subjects,
            'grades' => $grades
        ]);
    }

    public function postEditStaff($id, Request $request)
    {
        $request->validate([
            'fullname' => 'required',
            'email' => 'nullable|email',
            'address' => 'required',
            'dob' => 'required',
        ], [
            'fullname.required' => __('validation.required', ['attribute' => 'tên đầy đủ']),
            'email.email' => __('validation.email'),
            'address.required' => __('validation.required', ['attribute' => 'địa chỉ']),
            'dob.required' => __('validation.required', ['attribute' => 'ngày sinh']),
        ]);

        $data = request()->only([
            'fullname',
            'dob',
            'gender',
            'ethnic',
            'religion',
            'nationality',
            'address',
            'identity_card',
            'phone_number',
            'email',
            'qualification',
            'position',
            'school_branch_id',
            'status',
            'responsible',
            'professional_certificate',
            'subjects',
            'grades',
            'has_baby',
            'has_pregnant',
            'is_linking_staff'
        ]);

        foreach(['has_baby','has_pregnant', 'is_linking_staff'] as $field) {
            $data[$field] = 0;
            if(isset($request->$field) && $request->$field == 'on') $data[$field] = 1;
        }

        $result = $this->staffService->update($id, $data);

        //if(isset($result['validator']))  return redirect()->back()->withErrors($result['validator'])->withInput();

        return redirect()->back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Delete staff
     *
     * @return JsonResponse
     */
    public function deleteStaff()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => 'Method not allow!']);
        } else {
            $ids = request('ids');
            $staff = SchoolStaff::whereIn('id', $ids)->first();
            if ($staff) $this->saveActivityLog('Xóa nhân viên: "' . $staff->fullname . '"', $staff->school_id, $staff->school_branch_id);
            SchoolStaff::destroy($ids);

            return response()->json(['error' => 0, 'msg' => '']);
        }
    }

    public function importStaff($id)
    {
        return $this->renderView('admin.school.staff.import_staff', [
            'school' => School::where('id', $id)->with('district', 'district.province')->first()
        ]);
    }

    public function postImportStaff($id)
    {
        $school = School::where('id', $id)->with('district.province')->first();
        if (is_null($school)) {
            return redirect()->back()->with('error', 'Dữ liệu không đúng. Vui lòng kiểm tra lại!');
        }

        $validator = Validator::make(request()->all(), [
            'file_upload' => 'required|file',
        ], [
            'file_upload.required' => trans('validation.file_required'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        /* Validate Heading */
        $heading = (new HeadingRowImport)->toArray(request()->file('file_upload'))[0][0];
        if (!ImportStaff::validateFileHeader($heading)) {
            return redirect()->back()->with('error', 'Excel header không trùng. Vui lòng kiểm tra lại!');
        }

        $branches = $school->branches;
        $branchId = null;

        if (count($branches) === 1) {
            $branchId = $branches->first()->id;
        }

        $results = (new ImportStaff)->toArray(request()->file('file_upload'))[0];
        $results = ImportStaff::mappingKey($results);
        $results = ImportStaff::filterData($results);

        $resultIdentityCards = array_filter(Arr::pluck($results, 'identity_card'));
        foreach($resultIdentityCards as $x => $y) {
            $resultIdentityCards[$x] = intval($y);
        }
        $countCards = (array_count_values($resultIdentityCards));
        foreach($countCards as $card => $total) {
            if ($total > 1) {
                return redirect()->back()->with('error', "Số chứng minh thư {$card} được sử dụng nhiều lần trong file import. Vui lòng kiểm tra lại.");
            }
        }
        
        if (count($resultIdentityCards) > 0) {
            $staffExitsIdentityCard = SchoolStaff::whereIn('identity_card', $resultIdentityCards)->where('school_id', $id)->pluck('identity_card')->toArray();
            if (count($staffExitsIdentityCard) > 0) {
                $results = array_filter($results, function ($result) use ($staffExitsIdentityCard) {
                    return !in_array($result['identity_card'], $staffExitsIdentityCard);
                });
            }
        }

        //Set Staff Code
        if (count($results) == 0) {
            return redirect()->back()->with('error', 'Không thể import nhân viên do thôgn tin định danh (chứng minh thư) đã tồn tại trong hệ thống');
        }


        $validator = ImportStaff::validator($results);
        if ($validator->fails()) {
            $message = ImportStaff::getErrorMessage($validator->errors());
            $validator->getMessageBag()->add('file_upload', $message);
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $currentExist = $school->getLastestStaffCode();
            $data_activity = "Import nhân viên:<br>";
            foreach ($results as $index => $staff) {
                $schoolPrefix = School::getAccountPrefix($school->district, $school->school_type);

                $no = $currentExist + $index + 1;
                $staffCode = $school->generateStaffCode($no);

                //Render Data and create
                $staff['staff_code'] = $staffCode;
                $staff['school_id'] = $school->id;
                $staff['school_branch_id'] = $branchId;

                $dob = $staff['dob'];
                if (is_numeric($dob)) {
                    //Excel date serial format
                    $UNIX_DATE = ($dob - 25569) * 86400;
                    $staff['dob'] = gmdate("Y-m-d", $UNIX_DATE);
                } elseif (strpos($dob, '/')) {
                    list($day, $month, $year) = explode("/", $dob);
                    $staff['dob'] = $year . '-' . $month . '-' . $day;
                } elseif (strpos($dob, '-')) {
                    list($day, $month, $year) = explode("-", $dob);
                    $staff['dob'] = $year . '-' . $month . '-' . $day;
                }

                /** @var $newStaff SchoolStaff */
                $newStaff = SchoolStaff::create($staff);
                $data_activity .= $newStaff->fullname . '<br>';
                if ($newStaff->canCreateAccount()) {
                    $newStaff->createAccount();
                }
            }
            $activity = "Import nhân viên tại trường";
            $this->saveActivityLog($activity, $school->id, null, $data_activity);
            DB::commit();
            return redirect()
                ->route('admin.school.view_staff_list', ['id' => $school->id])
                ->with('success', 'Nhập dữ liệu nhân viên thành công!');
        } catch (Exception $ex) {
            DB::rollback();
            if(env('APP_ENV') !== 'production') dd($ex);
        }
    }

    public function assignClass(Request $request)
    {
        if ($request->isMethod('post')) {
            dd($request->input());
        }
    }

    public function manageTeacherGradeAndSubject(Request $request, $schoolId) {
        if ($request->isMethod('post')) {
                //dd($request->staffs);
            foreach($request->staffs as $staffId => $staffData) {
                if(isset($staffData['subjects'])) $this->staffService->updateSubjects($staffId, $staffData['subjects']); else $this->staffService->updateSubjects($staffId, []);
                if(isset($staffData['grades']))  $this->staffService->updateGrades($staffId, $staffData['grades']);  else $this->staffService->updateGrades($staffId, []);
            }
            
            return redirect()->back()->with('success', 'Cập nhật thành công');
        }

        $data =  $this->staffService->allBySchool($schoolId);
        $data['grades'] = $this->commonService->getGradeBySchoolType($data['school']->school_type);
        $data['subjects'] = $this->subjectService->getAllBySchool($schoolId);

        return view('admin.school.staff.manage_teacher_group_and_subject', $data);
    }

    public function timetable(Request $request, $schoolId, $staffId) {
        $data['school'] = $this->schoolService->findById($schoolId);
        $data['staff'] = $this->staffService->findById($staffId);
        $data['timetable'] = $this->timetableService->findByStaff($schoolId, $staffId);
        return view('admin.school.staff.timetable',$data);
    }

    public function regularGroups(Request $request, $schoolId, $staffId) {
        $data['staffId'] = $staffId;
        $data['school'] = $this->schoolService->findById($schoolId);
        $data['regularGroups'] =  $this->rgService->allByStaff($staffId);
        return view('admin.school.staff.regular_group', $data);
    }

    public function exportTimetable(Request $request, $schoolId, $staffId) {
        $data['school'] = $this->schoolService->findById($schoolId);
        $data['staff'] = $this->staffService->findById($staffId);
        $data['timetable'] = $this->timetableService->findByStaff($schoolId, $staffId);

        return Excel::download(new ScheduleExport($data), 'schedule_' . rand() . '.xlsx');
    }
}