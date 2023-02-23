<?php

namespace App\Admin\Services;

use App\Admin\Admin;
use App\Admin\Repositories\TeacherLessonRepository;
use App\Models\TeacherLessonHistory;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class TeacherLessonService
{
    protected $teacherLessonRepo;
    public function __construct(TeacherLessonRepository $teacherLessonRepo)
    {
        $this->teacherLessonRepo = $teacherLessonRepo;
    }

    public function addHistory($plan, $content, $createdBy) {
        $history = TeacherLessonHistory::create([
            'teacher_lesson_id' => $plan->id,
            'notes' => $content,
            'status' => $plan->status,
            'created_by'=>$createdBy
        ]);
    }

    public function findSubmittedByPlans($teacherPlans){
        return $this->teacherLessonRepo->findSubmittedTeacherLessonByPlans($teacherPlans->pluck('id')->toArray());
    }

}
