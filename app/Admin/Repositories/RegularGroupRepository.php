<?php

namespace App\Admin\Repositories;

use App\Models\RegularGroup;
use App\Models\RegularGroupGrade;
use App\Models\RegularGroupStaff;
use App\Models\RegularGroupSubject;
use Illuminate\Database\Eloquent\Model;

class RegularGroupRepository extends BaseRepository
{
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(RegularGroup $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function findBySchoolId($schoolId) {
        return $this->model->with(['groupPlans','groupSubjects', 'groupGrades', 'subjects', 'groupStaffs', 'groupStaffs.staff', 'leader', 'deputies'])->where('school_id', $schoolId)->get();
    }

    public function findByStaffId($staffId) {
        return $this->model->with(['groupSubjects', 'groupGrades', 'subjects', 'groupStaffs', 'groupStaffs.staff', 'leader', 'deputies'])->whereIn('id', RegularGroupStaff::where('staff_id', $staffId)->pluck('regular_group_id')->toArray())->get();
    }

    public function findByGroupPlanId($planId){
        return $this->model->whereHas('groupPLans', function ($query) use ($planId){
            $query->where('id', $planId);
        })->first();
    }

    public function getDefaultRegularGroupBySchoolLevel($schoolLevel) {
        return $this->model->with(['groupSubjects', 'groupGrades'])->where(['school_level' => $schoolLevel, 'school_id' => null ])->get();
    }

    public function cloneRegularGroup($regularGroup, $school){
        $subjects = $regularGroup->groupSubjects->pluck('subject_id')->toArray();
        $grades = $regularGroup->groupGrades->pluck('grade')->toArray();
        $schoolRegularGroup = $regularGroup->replicate();
        $schoolRegularGroup->school_id = $school->id;
        $schoolRegularGroup->push();
        $this->setSubjects($schoolRegularGroup->id, $subjects);
        $this->setGrades($schoolRegularGroup->id, $grades);
    }

    public function setSubjects($id, $subjects) {
        RegularGroupSubject::where('regular_group_id', $id)->delete();
        foreach ( $subjects as $subject ) {
            RegularGroupSubject::create([
                'regular_group_id' => $id,
                'subject_id' => $subject
            ]);
        }
    }

    public function setGrades($id, $grades) {
        RegularGroupGrade::where('regular_group_id', $id)->delete();
        foreach ( $grades as $grade ) {
            RegularGroupGrade::create([
                'regular_group_id' => $id,
                'grade' => $grade
            ]);
        }
    }
}



