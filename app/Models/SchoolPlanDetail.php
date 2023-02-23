<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolPlanDetail extends Model
{
    protected $table = 'school_plan_detail';
    protected $dateFormat = 'Y-m-d H:i:s';

    protected $fillable = [
        'school_plan_id',
        'grade',
        'thoi_gian_to_chuc_theo_tuan',
        'ke_hoach_cac_mon'
    ];

    public static function boot()
    {
        parent::boot();
       
    }
    
} 
