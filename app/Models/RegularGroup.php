<?php

namespace App\Models;

use App\Admin\Models\AdminUser;
use Illuminate\Database\Eloquent\Model;

class RegularGroup extends Model
{
    protected $table = 'regular_group';
    protected $dateFormat = 'Y-m-d H:i:s';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'name',
        'description',
        'school_level',
        'school_id'
    ];

    public static function boot()
    {
        parent::boot();
        self::deleted(function ($model) {
            $model->groupSubjects()->delete();
        });
    }

    public function school(){
        return $this->belongsTo(School::class, 'school_id', 'id');
    }

    public function groupSubjects() {
        return $this->hasMany(RegularGroupSubject::class, 'regular_group_id', 'id');
    }

    public function groupGrades() {
        return $this->hasMany(RegularGroupGrade::class, 'regular_group_id', 'id');
    }

    public function subjects() {
        return $this->hasManyThrough(Subject::class, RegularGroupSubject::class,'regular_group_id',  'id', 'id', 'subject_id');
    }

    public function groupStaffs() {
        return $this->hasMany(RegularGroupStaff::class, 'regular_group_id', 'id');
    }

    public function staffs() {
        return $this->hasManyThrough(SchoolStaff::class, RegularGroupStaff::class,'regular_group_id',  'id', 'id', 'staff_id');
    }

    public function leader() {
        return $this->hasOne(RegularGroupStaff::class, 'regular_group_id', 'id')->where('member_role', 1);
    }

    public function deputies() {
        return $this->hasMany(RegularGroupStaff::class, 'regular_group_id', 'id')->where('member_role', 2);
    }

    public function groupPlans() {
        return $this->hasMany(RegularGroupPlan::class, 'regular_group_id', 'id');
    }

    public function leaderAccount() {
        return AdminUser::where('username', $this->leader->staff->staff_code??null)->first() ?? null;
    }
}
