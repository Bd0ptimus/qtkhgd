<?php

namespace App\Admin\Repositories;

use App\Models\RegularGroup;
use App\Models\RegularGroupPlan;
use App\Models\School;
use Illuminate\Database\Eloquent\Model;

class RegularGroupPlanRepository extends BaseRepository
{
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(RegularGroupPlan $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function findPendingPlanBySchool($schoolId) {
        return $this->model->with(['group', 'subjectPlans','planSubject', 'histories', 'group.leader'])->whereIn('status', [PLAN_INREVIEW, PLAN_SUBMITTED])->whereIn('regular_group_id', RegularGroup::where('school_id', $schoolId)->pluck('id')->toArray())->get();
    }

    public function findApprovedPlanByDistrict($districtId) {
        return $this->model->with(['group', 'group.school', 'subjectPlans', 'histories', 'group.leader'])
        ->where('status', PLAN_APPROVED)
        ->whereIn('regular_group_id', RegularGroup::whereIn('school_id', School::where('district_id', $districtId)->pluck('id')->toArray())->pluck('id')->toArray())->get();
    }


    public function findApprovedPlanByDistrictWithConditions($districtId, $params=[]) {
        $query = $this->model->with(['group', 'group.school', 'subjectPlans', 'histories', 'group.leader'])->newQuery();
        // dd($query->whereIn('status', [PLAN_APPROVED])->get());
        if(isset($params['level'])){
            $query->whereHas('group.school', function($query) use ($params){
                $query->where('school_type', $params['level']);
            });
        }

        if(isset($params['school'])){
            $query->whereHas('group.school', function($query) use ($params){
                $query->where('id',$params['school']);
            });
        }

        if(isset($params['subject'])){
            $query->where('subject', $params['subject']);
        }

        return $query->where('status', PLAN_APPROVED)
        ->whereIn('regular_group_id', RegularGroup::whereIn('school_id', School::where('district_id', $districtId)->pluck('id')->toArray())->pluck('id')->toArray())->get();
    }
}