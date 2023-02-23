<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeSubject extends Model
{
    protected $table = 'grade_subject';
    protected $dateFormat = 'Y-m-d H:i:s';
    public $timestamps = false;

    protected $fillable = [
        'grade',
        'subject_id'
    ];
}
