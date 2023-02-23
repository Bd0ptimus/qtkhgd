<?php

namespace App\Models;

use App\Models\School;
use App\Models\Simulator;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $table = 'subject';
    protected $dateFormat = 'Y-m-d H:i:s';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const TIENG_VIET = 1;
    const TOAN = 2;
    const DAO_DUC = 3;
    const TU_NHIEN_VA_XA_HOI = 4;
    const KHOA_HOC = 5;
    const LICH_SU_VA_DIA_LY = 6;

    const PRIMARY_SCHOOL_SUBJECT_DEFAULT = [
        self::TIENG_VIET,
        self::TOAN,
        self::DAO_DUC,
        self::TU_NHIEN_VA_XA_HOI,
        self::KHOA_HOC,
        self::LICH_SU_VA_DIA_LY,
    ];

    protected $fillable = [
        'name',
        'description',
        'school_id'
    ];

    public static function boot()
    {
        parent::boot();
        self::deleted(function ($model) {
            $model->grades()->delete();
        });
    }

    public function grades() {
        return $this->hasMany(GradeSubject::class, 'subject_id', 'id');
    }

    public function school(){
        return $this->belongsTo(School::class,'school_id', 'id');
    }

    public function subjectStaffs() {
        return $this->hasMany(StaffSubject::class, 'subject_id', 'id');
    }

    public function teachers() {
        return $this->hasManyThrough(SchoolStaff::class, StaffSubject::class, 'subject_id', 'id', 'id', 'staff_id');
    }

    public function simulators(){
        return $this->hasMany(Simulator::class, 'subject_id', 'id');
    }
}
