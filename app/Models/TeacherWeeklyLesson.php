<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TeacherWeeklyLesson extends Model
{

    public $table = 'teacher_weekly_lesson';

    protected $guarded = [];

    public function teacherLesson()
    {
        return $this->belongsTo(TeacherLesson::class);
    }

    public function getProgressByYear()
    {
        if (!$this->teacherLesson->start_date || !$this->start_date) {
            return 'Không thể xác định';
        }
        $startDateByPlan = new Carbon($this->teacherLesson->start_date);
        $startDateActual = new Carbon($this->start_date);
        if ($startDateActual->gt($startDateByPlan)) {
            return 'Trễ tiến độ';
        } elseif($startDateActual->lt($startDateByPlan)) {
            return 'Nhanh so với kế hoạch năm';
        } else {
            return 'Tiến độ chuẩn';
        }
    }

    public function getProgressByWeek()
    {
        $classes = [];
        foreach ($this->teacherWeeklyLessonProgresses as $progresses) {
            if (!$progresses->is_taught) {
                $classes[] = $progresses->schoolClass->class_name;
            }
        }

        if ($classes) {
            return 'Lớp ' . implode(', ', $classes) . ' chưa hoàn thành';
        }

        return 'Tất cả các lớp đã hoàn thành';
    }

    public function getStartDateAttribute($value)
    {
        if (!empty($value)) return date('d-m-Y', strtotime($value)); 
    }

    public function getEndDateAttribute($value)
    {
        if (!empty($value)) return date('d-m-Y', strtotime($value)); 
    }

    public function setStartDateAttribute($value)
    {
        if ($value) {
            $this->attributes['start_date'] = new Carbon($value);
        }
    }

    public function setEndDateAttribute($value)
    {
        if ($value) {
            $this->attributes['end_date'] = new Carbon($value);
        }
    }

    public function teacherWeeklyLessonProgresses()
    {
        return $this->hasMany(TeacherWeeklyLessonProgress::class);
    }
}