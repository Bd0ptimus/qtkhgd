<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegularGroupPlan extends Model
{
    protected $table = 'regular_group_plan';
    protected $dateFormat = 'Y-m-d H:i:s';

    protected $fillable = [
        'regular_group_id',
        'grade', 'subject', 'month',
        'can_cu_xay_dung',
        'dieu_kien_thuc_hien',
        'to_chuc_thuc_hien',
        'content',
        'status'
    ];

    public function planSubject() {
        return $this->belongsTo(Subject::class, 'subject', 'id');
    }

    public function subjectPlans() {
        return $this->hasMany(GroupSubjectPlan::class, 'group_plan_id', 'id');
    }

    public function group() {
        return $this->belongsTo(RegularGroup::class, 'regular_group_id', 'id');
    }

    public function histories() {
        return $this->hasMany(GroupPlanHistory::class, 'group_plan_id', 'id')->orderBy('created_at','DESC');
    }
}
