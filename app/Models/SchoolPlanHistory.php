<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolPlanHistory extends Model
{
    protected $table = 'school_plan_history';
    protected $dateFormat = 'Y-m-d H:i:s';   

    protected $fillable = [
        'school_plan_id',
        'notes',
        'status'
    ];

    public function schoolPlan() {
        return $this->belongsTo(SchoolPlan::class, 'school_plan_id', 'id');
    }
}
