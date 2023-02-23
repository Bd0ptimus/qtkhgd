<?php

namespace App\Admin\Repositories;

use App\Models\School;
use App\Models\SchoolStaff;
use App\Models\TeacherLesson;
use App\Models\TeacherPlan;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;

class TeacherPlanRepository extends BaseRepository
{
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(TeacherPlan $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function setLessons($id, $lessons) {

        TeacherLesson::where('teacher_plan_id', $id)->delete();

        foreach ($lessons as $lesson ) {
            /* Ignore deleted Items */
            if(count($lesson) != 1) {
                /* Process New Items */
                try{
                    list($start, $end) = explode('-', $lesson['khoang_thoi_gian'] ?? [null,null]);
                } catch (Exception $e) {
                    dd($lesson);
                }

                $lesson['teacher_plan_id'] = $id;
                $lesson['start_date'] = $start ? Carbon::createFromFormat('d/m/Y', trim($start)) : null;
                $lesson['end_date'] = $end ? Carbon::createFromFormat('d/m/Y', trim($end)) : null;
                TeacherLesson::create($lesson);
            }
        }
    }

    public function findByStaffAndGroup($staffId, $rgId) {
        return $this->model->where([
            'regular_group_id' => $rgId,
            'staff_id' => $staffId
        ])->with(['subject', 'lessons', 'staff', 'histories'])->get();
    }
    
    public function findBySchoolId($schoolId) {
        $school = School::find($schoolId);
        $staff = $this->model->whereIn('staff_id',SchoolStaff::where('school_id',$schoolId)->pluck('id')->toArray() )->with(['subject','regularGroup', 'lessons', 'staff', 'histories'])->get();
       
        return [
            'school' => $school,
            'staff' => $staff
    ];
    }
   

    public function findPendingPlanByGroup($rgId) {
        return $this->model->where([
            'regular_group_id' => $rgId,
        ])->whereIn('status', [PLAN_SUBMITTED, PLAN_INREVIEW])->with(['subject', 'lessons', 'staff', 'histories'])->get();
    }

    public function findApprovedPlanByGroup($rgId, $staffId = null) {
        $condition = ['regular_group_id' => $rgId];
        if($staffId) $condition['staff_id'] = $staffId;
        return $this->model->where($condition)->whereIn('status', [PLAN_APPROVED])->with(['subject', 'lessons', 'staff', 'histories'])->get();
    }

    public function findApprovedPlanByStaff($staffId) {
        return $this->model->where(['staff_id' => $staffId])->whereIn('status', [PLAN_APPROVED])->with(['subject', 'lessons', 'staff', 'histories', 'lessons.histories', 'lessons.histories.createdBy'])->get();
    }

    public function findApprovedPlanByDistrict($districtId) {
        return $this->model->with(['subject', 'lessons', 'staff', 'histories', 'staff.school'])
        ->where('status', PLAN_APPROVED)
        ->whereIn('staff_id', SchoolStaff::whereIn('school_id', School::where('district_id', $districtId)->pluck('id')->toArray())->pluck('id')->toArray())->get();
    }

    public function findApprovedPlanByDistrictWithConditions($districtId, $params=[]) {
        $query= $this->model->with(['subject', 'lessons', 'staff', 'histories','regularGroup' ,'staff.school'])->newQuery();
        // dd($query->whereIn('status', [PLAN_APPROVED])->get());
        if(isset($params['level'])){
            $query->whereHas('regularGroup', function($query) use ($params){
                $query->where('school_level', $params['level']);
            });
        }

        if(isset($params['school'])){
            $query->whereHas('regularGroup', function($query) use ($params){
                $query->where('school_id',$params['school']);
            });
        }

        if(isset($params['subject'])){
            $query->where('subject_id', $params['subject']);
        }
        return $query->where('status', PLAN_APPROVED)
        ->whereIn('staff_id', SchoolStaff::whereIn('school_id', School::where('district_id', $districtId)->pluck('id')->toArray())->pluck('id')->toArray())->get();
    }
}