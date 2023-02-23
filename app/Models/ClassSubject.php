<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassSubject extends Model
{
    protected $table = 'class_subject';
    protected $dateFormat = 'Y-m-d H:i:s';
    public $timestamps = false;

    protected $fillable = [
        'staff_id',
        'subject_id',
        'class_id',
        'lesson_per_week'
    ];

    public function subject() {
        return $this->belongsTo(Subject::class, 'subject_id', 'id');
    }

    public function staff() {
        return $this->belongsTo(SchoolStaff::class, 'staff_id', 'id');
    }

    public function class() {
        return $this->belongsTo(SchoolClass::class, 'class_id', 'id');
    }

    public function staffSubjects() {
        return $this->hasManyThrough(StaffSubject::class, Subject::class, 'id', 'subject_id', 'subject_id', 'id');
    }
}
