<?php

namespace App\Models;

use App\Models\SchoolClass;
use Illuminate\Database\Eloquent\Model;

class TeacherWeeklyLessonProgress extends Model
{
    protected $fillable = [
        'teacher_weekly_lesson_id',
        'class_id',
        'is_taught',
    ];

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id', 'id');
    }
}
