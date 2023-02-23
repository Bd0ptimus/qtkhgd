<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffGrade extends Model
{
    protected $table = 'staff_grade';
    protected $dateFormat = 'Y-m-d H:i:s';
    public $timestamps = false;

    protected $fillable = [
        'staff_id',
        'grade'
    ];
}
