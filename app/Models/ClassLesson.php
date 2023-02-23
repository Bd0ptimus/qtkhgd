<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassLesson extends Model
{
    protected $table = 'class_lesson';
    protected $dateFormat = 'Y-m-d H:i:s';

    protected $fillable = [
        'class_id',
        'timetable_id',
        'class_subject_id',
        'slot'
    ];

    public function classSubject() {
        return $this->belongsTo(ClassSubject::class, 'class_subject_id', 'id');
    }

} 
