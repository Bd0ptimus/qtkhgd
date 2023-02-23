<?php

namespace App\Models;

use App\Admin\Models\AdminUser;
use App\Models\Base\BaseModel as Model;

class District extends Model
{
    private static $getList = null;
    public $table = 'districts';
    protected $guarded = [];

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id', 'id');
    }

    public function schools()
    {
        return $this->hasMany(School::class, 'district_id', 'id');
    }

    //Check if has pgd account - Phòng Giáo Dục

    public function wards()
    {
        return $this->hasMany(Ward::class, 'district_id', 'id');
    }

    //Return all District with user role

    public function users()
    {
        return $this->morphToMany(AdminUser::class, 'agency', 'user_agency', 'agency_id', 'user_id', 'id', 'id');
    }

    public function reports()
    {
        return $this->hasMany(SchoolReport::class, 'district_id', 'id');
    }

    public function getTotalStudentAttribute()
    {
        return $this->schools->reduce(function ($count, $school) {
            return $count + $school->students->count();
        }, 0);
    }

    public function getTotalStaffAttribute()
    {
        return $this->schools->reduce(function ($count, $school) {
            return $count + $school->staffs->count();
        }, 0);
    }

    public function getTotalInsuranceAttribute()
    {
        return $this->schools->reduce(function ($count, $school) {
            return $count + $school->total_insurance;
        }, 0);
    }

    public function getTotalBhTunguyenAttribute()
    {
        return $this->schools->reduce(function ($count, $school) {
            return $count + $school->total_bh_tunguyen;
        }, 0);
    }

    public function getTotalBhChinhsachAttribute()
    {
        return $this->schools->reduce(function ($count, $school) {
            return $count + $school->total_bh_chinhsach;
        }, 0);
    }

    /*Thống kê bệnh truyền nhiễm */
    public function getTotalTieuchayAttribute()
    {
        return $this->schools->reduce(function ($count, $school) {
            $diagnosis_tieuchay = 21;
            return $count + $school->countingStudentsWithInfectiousDiseases($diagnosis_tieuchay);
        }, 0);
    }

    public function getTotalChantaymiengAttribute()
    {
        return $this->schools->reduce(function ($count, $school) {
            $diagnosis_chantaymieng = 22;
            return $count + $school->countingStudentsWithInfectiousDiseases($diagnosis_chantaymieng);
        }, 0);
    }
    
    public function getTotalSoiAttribute()
    {
        return $this->schools->reduce(function ($count, $school) {
            $diagnosis_soi = 23;
            return $count + $school->countingStudentsWithInfectiousDiseases($diagnosis_soi);
        }, 0);
    }
    
    public function getTotalQuaibiAttribute()
    {
        return $this->schools->reduce(function ($count, $school) {
            $diagnosis_quaibi = 24;
            return $count + $school->countingStudentsWithInfectiousDiseases($diagnosis_quaibi);
        }, 0);
    }
    
    public function getTotalCumAttribute()
    {
        return $this->schools->reduce(function ($count, $school) {
            $diagnosis_cum = 25;
            return $count + $school->countingStudentsWithInfectiousDiseases($diagnosis_cum);
        }, 0);
    }
    
    public function getTotalRubellaAttribute()
    {
        return $this->schools->reduce(function ($count, $school) {
            $diagnosis_rubella = 26;
            return $count + $school->countingStudentsWithInfectiousDiseases($diagnosis_rubella);
        }, 0);
    }
    
    public function getTotalSotxuathuyetAttribute()
    {
        return $this->schools->reduce(function ($count, $school) {
            $diagnosis_sotxuathuyet = 27;
            return $count + $school->countingStudentsWithInfectiousDiseases($diagnosis_sotxuathuyet);
        }, 0);
    }
    
    public function getTotalThuydauAttribute()
    {
        return $this->schools->reduce(function ($count, $school) {
            $diagnosis_thuydau = 28;
            return $count + $school->countingStudentsWithInfectiousDiseases($diagnosis_thuydau);
        }, 0);
    }
    
    public function getTotalSars_cov_2Attribute()
    {
        return $this->schools->reduce(function ($count, $school) {
            $diagnosis_sars_cov_2 = 29;
            return $count + $school->countingStudentsWithInfectiousDiseases($diagnosis_sars_cov_2);
        }, 0);
    }
        
    public function getTotalKhacAttribute()
    {
        return $this->schools->reduce(function ($count, $school) {
            $diagnosis_khac = 30;
            return $count + $school->countingStudentsWithInfectiousDiseases($diagnosis_khac);
        }, 0);
    }
    
    /*Thống kê tổng số trường và học sinh */
    public function getTotalThptAttribute()
    {
        return $this->schools->reduce(function ($count, $school) {
            $is_thpt = in_array($school->school_type, [3,5]) ? 1 : 0;
            return $count + $is_thpt;
        }, 0);
    }

    public function getTotalStAttribute()
    {
        return $this->schools->reduce(function ($count, $school) {
            $is_thpt = in_array($school->school_type, [3,5]) ? 1 : 0;
            return $count + $is_thpt;
        }, 0);
    }

    public function tongHopTiemChungVaKiemTraSK($schoolsData) {
        $result['staffs'] = [
            'total_staff' => 0,
            'f0' => 0,
            'f1' => 0,
            'f2' => 0,
            'tiem_1m' => 0,
            'tiem_2m' => 0,
            'tiem_3m' => 0,
            'tiem_4m' => 0,
            'chua_tiem' => 0,
            'tong_ca_sang' => 0,
            'vs_do_covid' => 0,
            'vs_ko_covid' => 0,
            'tong_ca_chieu' => 0,
            'vc_do_covid' => 0,
            'vc_ko_covid' => 0,
        ];

        $result['students'] = [
            'total_student' => 0,
            'f0' => 0,
            'f1' => 0,
            'f2' => 0,
            'tiem_1m' => 0,
            'tiem_2m' => 0,
            'tiem_3m' => 0,
            'tiem_4m' => 0,
            'chua_tiem' => 0,
            'tong_ca_sang' => 0,
            'vs_do_covid' => 0,
            'vs_ko_covid' => 0,
            'tong_ca_chieu' => 0,
            'vc_do_covid' => 0,
            'vc_ko_covid' => 0,
        ];

        foreach ([1,2,3,4,5,6,7,8,9,13,14,15,131415,16,17,18] as $grade) {
            $result['grade'][$grade] = [
                'total_student' => 0,
                'total_classes' => 0,
                'online_classes' => 0,
                'offline_classes' => 0,
                'f0' => 0,
                'f1' => 0,
                'f2' => 0,
                'tiem_1m' => 0,
                'tiem_2m' => 0,
                'tiem_3m' => 0,
                'tiem_4m' => 0,
                'chua_tiem' => 0,
                'tong_ca_sang' => 0,
                'vs_do_covid' => 0,
                'vs_ko_covid' => 0,
                'tong_ca_chieu' => 0,
                'vc_do_covid' => 0,
                'vc_ko_covid' => 0,
            ];
        }

        if(count($schoolsData) > 0) {
            foreach($schoolsData as $schoolData) {
                foreach($schoolData['staffs'] as $key => $value)
                {
                    $result['staffs'][$key] += $value;
                }

                foreach($schoolData['students'] as $key => $value)
                {
                    $result['students'][$key] += $value;
                }

                foreach($schoolData['grade'] as $grade => $data)
                {
                    foreach($data as $key => $value)
                    {
                        $result['grade'][$grade][$key] += $value;
                    }
                }
            }
        }

        return $result;
    }
}