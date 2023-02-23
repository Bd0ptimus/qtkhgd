<?php

namespace App\Admin\Repositories;

use App\Models\School;
use App\Models\SchoolPlan;
use App\Models\Timetable;
use Illuminate\Database\Eloquent\Model;

class SchoolPlanRepository extends BaseRepository
{
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(
        SchoolPlan $model
    )
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function findBySchoolId($schoolId) {
        return $this->model->where('school_id', $schoolId)->with('gradeDetails')->get();
    }

    public function findPendingPlanBySchool() {
        
    }
    
    public function findPendingPlanByDistrict($districtId) {
        return $this->model->with('school')->whereIn('status', [PLAN_INREVIEW, PLAN_SUBMITTED])->whereIn('school_id', School::where('district_id', $districtId)->pluck('id')->toArray())->get();
    }

    public function findApprovedPlanByDistrictWithConditions($districtId, $params=[]) {
        $query = $this->model->with('school')->newQuery();
        // dd($query->whereIn('status', [PLAN_APPROVED])->get());
        if(isset($params['level'])){
            $query->whereHas('school', function($query) use ($params){
                $query->where('school_type', $params['level']);
            });
        }

        if(isset($params['school'])){
            $query->whereHas('school', function($query) use ($params){
                $query->where('id',$params['school']);
            });
        }
        return $query->whereIn('status', [PLAN_APPROVED])->whereIn('school_id', School::where('district_id', $districtId)->pluck('id')->toArray())->get();

    }

    public function findApprovedPlanByDistrict($districtId) {
        return $this->model->with('school')->whereIn('status', [PLAN_APPROVED])->whereIn('school_id', School::where('district_id', $districtId)->pluck('id')->toArray())->get();
    }
}