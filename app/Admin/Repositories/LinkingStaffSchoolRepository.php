<?php

namespace App\Admin\Repositories;

use App\Models\StaffLinkingSchool;
use App\Models\StaffSubject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class LinkingStaffSchoolRepository extends BaseRepository
{
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(StaffLinkingSchool $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function takeLinkingStaffSchoolInfo($staffId, $additionalSchoolId){
        return StaffLinkingSchool::where([
            ['staff_id', $staffId],
            ['additional_school_id',$additionalSchoolId]
        ])->get();
    }

    public function takeLinkingStaffSubject($staffs){
        $staffSubjects=array();
        foreach($staffs as $staff){
            foreach(StaffSubject::where('staff_id', $staff->id)->get() as $subjectId){
                array_push($staffSubjects,$subjectId);
            }
        }
        //dd($staffSubjects);
        return $staffSubjects;
    }
}
