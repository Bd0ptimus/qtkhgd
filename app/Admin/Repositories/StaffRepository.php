<?php

namespace App\Admin\Repositories;

use App\Models\ClassSubject;
use App\Models\SchoolStaff;
use App\Models\StaffGrade;
use App\Models\StaffLinkingSchool;
use App\Models\StaffSubject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use App\Admin\Helpers\Utils;

class StaffRepository extends BaseRepository
{
    protected $model;
    private $utilHeper;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(SchoolStaff $model, Utils $utilHelper)
    {
        parent::__construct($model);
        $this->model = $model;
        $this->utilHeper = $utilHelper;
    }

    public function findBySchoolId($schoolId)
    {
        return $this->model->with(['staffGrades', 'subjects', 'staffSubjects'])->where('school_id', $schoolId)->get();
    }

    public function findByCode($staffCode)
    {
        return $this->model->where('staff_code', $staffCode)->first();
    }

    public function setSubjects($id, $subjects)
    {
        StaffSubject::where('staff_id', $id)->delete();
        foreach ($subjects as $subject) {
            StaffSubject::create([
                'staff_id' => $id,
                'subject_id' => $subject
            ]);
        }
    }

    public function setGrades($id, $grades)
    {
        StaffGrade::where('staff_id', $id)->delete();
        foreach ($grades as $grade) {
            StaffGrade::create([
                'staff_id' => $id,
                'grade' => $grade
            ]);
        }
    }

    public function setLinkingStaff($id, $linkingSchools)
    {
        $dataReturn['staff_id'] = $id;
        try {
            $arrUpdate = [];
            foreach ($linkingSchools as $key => $school) {
                if ($school['school_id'] == 'Chọn' || $school['school_id'] == '') {
                   continue;
                }
                $arrUpdate[$school['school_id']]['days'][] = $key + 2;
                $arrUpdate[$school['school_id']]['slots'][] = $school['slots'];
            }
            // update
            foreach ($arrUpdate as $schoolId => $update) {
                $slots = call_user_func_array('array_merge', $update['slots']);
                StaffLinkingSchool::where([
                    ['staff_id', '=', $id],
                    ['additional_school_id', '=', $schoolId],
                ])->update([
                    'working_days' => json_encode(['days' => $update['days']]),
                    'working_slots' => json_encode(['slots' => $slots])
                ]);
            }
            $dataReturn['workday'] = $linkingSchools;
        } catch (\Exception $e) {
            $dataReturn['workday'] = [];
            $dataReturn['slots'] = [];
        }

        return $dataReturn;

        // $addNewLinkingSchool = false;// add new linking school or not

        // //update linking school with work day
        // foreach($workingDays as $key => $workingdaySchool){
        //     $workdayData['day']=array();
        //     if($workingdaySchool!=null && in_array($workingdaySchool, $linkingSchools)){
        //         array_push($workdayData['day'],$key+2);
        //         $addNewLinkingSchool=true;
        //     }            
        // StaffLinkingSchool::where([
        //     ['staff_id','=', $id],
        //     ['additional_school_id','=', $workingdaySchool],
        // ])->update(['working_days'=>json_encode($workdayData)]);
        // }

        //create new linking school
        // if($addNewLinkingSchool==false){
        //     $staff = $this->model->find($id);
        //     StaffLinkingSchool::where('staff_id', $id)->delete();
        //     foreach ( $linkingSchools as $linkingSchool ) {
        //         if($linkingSchool != null)
        //         StaffLinkingSchool::create([
        //             'staff_id' => $id,
        //             'primary_school_id' => $staff->school_id,
        //             'additional_school_id' => $linkingSchool,
        //             'working_days' => json_encode([])
        //         ]);
        //     }
        // }
    }

    public function getLinkingStaff($schoolId, $attribute){
        return StaffLinkingSchool::where($attribute,$schoolId)->get();
    }


    //lấy available slots của các nhân viên của các trường khác đang liên kết với trường hiện tại
    public function takeSlotlinkingStaffIn($linkingStaffsIn, $teacher)
    {
        $teacherAvailableSlots = [];
        $workingSlots = [];
        foreach ($linkingStaffsIn as $linkingStaff) {
            if ($linkingStaff->staff_id == $teacher->staff_id) {
                $workingSlots = json_decode($linkingStaff->working_slots);
            }
        }
        if(empty($workingSlots)) return [];
        foreach ($workingSlots->slots as $slot) {
            $teacherAvailableSlots[] = $slot;
        }
        //return các tiết thuộc ngày có liên kết
        return $teacherAvailableSlots;
    }

    //lấy available slots của các nhân viên của trường nhưng có liên kết với trường khác
    public function getLessonForLinkingStaffTo($staff){
        $teacherAvailableSlots = [
            'mon_1', 'mon_2', 'mon_3', 'mon_4', 'mon_5', 'mon_6', 'mon_7', 'mon_8', 'mon_9',
            'tue_1', 'tue_2', 'tue_3', 'tue_4', 'tue_5', 'tue_6', 'tue_7', 'tue_8', 'tue_9',
            'wed_1', 'wed_2', 'wed_3', 'wed_4', 'wed_5', 'wed_6', 'wed_7', 'wed_8', 'wed_9',
            'thu_1', 'thu_2', 'thu_3', 'thu_4', 'thu_5', 'thu_6', 'thu_7', 'thu_8', 'thu_9',
            'fri_1', 'fri_2', 'fri_3', 'fri_4', 'fri_5', 'fri_6', 'fri_7', 'fri_8', 'fri_9',
            'sat_1', 'sat_2', 'sat_3', 'sat_4', 'sat_5', 'sat_6', 'sat_7', 'sat_8', 'sat_9',
        ];
        foreach($staff->linkingInfo as $staffLinkingInfo){
            $busySlots = json_decode($staffLinkingInfo->working_slots);
            if(empty($busySlots)) continue;
            foreach($busySlots->slots as $slot){
                if (($key = array_search($slot, $teacherAvailableSlots)) !== false) {
                    unset($teacherAvailableSlots[$key]);
                }
            }
        }

        //Return tất cả các tiết, trừ các tiết thuộc ngày có liên kết
        return array_values($teacherAvailableSlots);
    }


    //lấy available slots của các nhân viên không liên kết
    public function getLessonSlotByStaff()
    {
        return [
            'mon_1', 'mon_2', 'mon_3', 'mon_4', 'mon_5', 'mon_6', 'mon_7', 'mon_8', 'mon_9',
            'tue_1', 'tue_2', 'tue_3', 'tue_4', 'tue_5', 'tue_6', 'tue_7', 'tue_8', 'tue_9',
            'wed_1', 'wed_2', 'wed_3', 'wed_4', 'wed_5', 'wed_6', 'wed_7', 'wed_8', 'wed_9',
            'thu_1', 'thu_2', 'thu_3', 'thu_4', 'thu_5', 'thu_6', 'thu_7', 'thu_8', 'thu_9',
            'fri_1', 'fri_2', 'fri_3', 'fri_4', 'fri_5', 'fri_6', 'fri_7', 'fri_8', 'fri_9',
            'sat_1', 'sat_2', 'sat_3', 'sat_4', 'sat_5', 'sat_6', 'sat_7', 'sat_8', 'sat_9',
        ];
    }

    public function getLessonSlotForPregnantOrWithChild($existedSlots){
        foreach($existedSlots as $key=>$existedSlot) {
            if(strpos($existedSlot, '1') !== false||strpos($existedSlot, '5') !== false){
                unset($existedSlots[$key]);
            }
        }       
        //dd($existedSlots);
        return $existedSlots;
    }

    public function takeSubjectAndGradeOfStaff($staffCode){
        $dataReturn['grades'] = [];
        $dataReturn['subjects'] = [];
        $staffId =  SchoolStaff::where('staff_code', $staffCode)->first(); //take staff info
        $dataReturn['subjects'] = StaffSubject::with('subject')->where('staff_id', $staffId->id)->pluck('subject_id')->toArray();
        $dataReturn['grades'] = StaffGrade::where('staff_id', $staffId->id)->pluck('grade')->toArray();

        $dataReturn['school_level'] = $this->utilHeper->takeSchoolLevel($dataReturn['grades'][0]);
        return $dataReturn;
    }

}
