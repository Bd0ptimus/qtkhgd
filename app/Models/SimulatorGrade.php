<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SimulatorGrade extends Model
{
    protected $table = 'simulator_grades';
    protected $dateFormat = 'Y-m-d H:i:s';
    public $timestamps = false;

    protected $fillable = [
        'simulator_id',
        'grade',
    ];

}
