<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolPlan extends Model
{
    protected $table = 'school_plan';
    protected $dateFormat = 'Y-m-d H:i:s';

    protected $fillable = [
        'school_id',
        'can_cu_1',
        'dac_diem_ktvhxh_21',  
        'dac_diem_hocsinh_221',  
        'tinh_hinh_nhan_vien_222',  
        'co_so_vat_chat_23',
        'mtnh_chung_31',
        'mtnh_cu_the_32',
        'phan_phoi_thoi_luong_41',
        'hd_tap_the_421',
        'hd_ngoai_gio_422',
        'to_chuc_thuc_hien_diem_truong_43',
        'khung_thoi_gian_44',
        'giai_phap_thuc_hien_5',
        'to_chuc_thuc_hien_6',
        'content',
        'status'
    ];

    public static function boot()
    {
        parent::boot();
       
    }

    public function gradeDetails() {
        return $this->hasMany(SchoolPlanDetail::class, 'school_plan_id', 'id');
    }

    public function school(){
        return $this->belongsTo(School::class, 'school_id', 'id');
    }

    public function histories() {
        return $this->hasMany(SchoolPlanHistory::class, 'school_plan_id', 'id');
    }
} 
