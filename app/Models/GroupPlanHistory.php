<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupPlanHistory extends Model
{
    protected $table = 'group_plan_history';
    protected $dateFormat = 'Y-m-d H:i:s';   

    protected $fillable = [
        'group_plan_id',
        'notes',
        'status'
    ];

    public function groupPlan() {
        return $this->belongsTo(RegularGroupPlan::class, 'group_plan_id', 'id');
    }
}
