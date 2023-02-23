<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegularGroupGrade extends Model
{
    protected $table = 'regular_group_grade';
    protected $dateFormat = 'Y-m-d H:i:s';
    public $timestamps = false;

    protected $fillable = [
        'regular_group_id',
        'grade'
    ];
}
