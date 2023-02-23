<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherPlanHistory extends Model
{
    protected $table = 'teacher_plan_history';
    protected $dateFormat = 'Y-m-d H:i:s';   

    protected $fillable = [
        'teacher_plan_id',
        'notes',
        'status'
    ];
    public function teacherPlan() {
        return $this->belongsTo(TeacherPlan::class,'teacher_plan_id', 'id');
    }
}
