<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\SimulatorGrade;
use App\Models\Subject;

class Simulator extends Model
{
    protected $table="simulators";
    
    protected $fillable = [
        'name_simulator',
        'subject_id',
        'related_lesson',
        'user_guide',
        'url_simulator',
    ];
    const PAGE_SIZE = 25;

    public function simulatorGrades(){
        return $this->hasMany(SimulatorGrade::class,'simulator_id','id');
    }
    public function subject(){
        return $this->belongsTo(Subject::class,'subject_id','id');
    }
}
