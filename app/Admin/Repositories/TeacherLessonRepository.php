<?php

namespace App\Admin\Repositories;

use App\Models\School;
use App\Models\SchoolStaff;
use App\Models\TeacherLesson;
use App\Models\TeacherPlan;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;

class TeacherLessonRepository extends BaseRepository
{
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(TeacherLesson $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function findSubmittedTeacherLessonByPlans($planIds){
        return TeacherLesson::whereIn('teacher_plan_id', $planIds)->where('status', PLAN_SUBMITTED)->get();
    }
}