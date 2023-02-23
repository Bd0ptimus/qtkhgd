<?php

namespace App\Admin\Models\Imports;

use App\Models\SchoolHealthInsurance;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ImportVaccineHistory extends BaseImport
{
    protected static $arrFields = [
        'form_type' => SchoolHealthInsurance::FORM_TYPE,
        'term_type' => SchoolHealthInsurance::TERM_TYPE,
    ];

    protected static $mappingHeader = [
        //'ma_hoc_sinh' => 'student_code',
        'ho_ten' => 'fullname',

        'ngay_tiem_m1' => 'm1_date',
        'loai_vaccine_m1'=> 'm1_loai_vc',
        'lo_vaccine_m1' => 'm1_lo_vc',
        'han_su_dung_m1' => 'm1_hsd',
        'don_vi_tiem_m1' => 'm1_dvt',

        'ngay_tiem_m2' => 'm2_date',
        'loai_vaccine_m2'=> 'm2_loai_vc',
        'lo_vaccine_m2' => 'm2_lo_vc',
        'han_su_dung_m2' => 'm2_hsd',
        'don_vi_tiem_m2' => 'm2_dvt',

        'ngay_tiem_m3' => 'm3_date',
        'loai_vaccine_m3'=> 'm3_loai_vc',
        'lo_vaccine_m3' => 'm3_lo_vc',
        'han_su_dung_m3' => 'm3_hsd',
        'don_vi_tiem_m3' => 'm3_dvt',

        'ngay_tiem_m4' => 'm4_date',
        'loai_vaccine_m4'=> 'm4_loai_vc',
        'lo_vaccine_m4' => 'm4_lo_vc',
        'han_su_dung_m4' => 'm4_hsd',
        'don_vi_tiem_m4' => 'm4_dvt',

    ];

    public static function validateFileHeader($heading)
    {
        foreach ($heading as $key => $value) {
            if ($value == null) unset($heading[$key]);
        }


        return $heading == [
                //0 => 'ma_hoc_sinh',
                0 => 'ho_ten',
                1 => 'ngay_tiem_m1',
                2 => 'loai_vaccine_m1',
                3 => 'lo_vaccine_m1',
                4 => 'han_su_dung_m1',
                5 => 'don_vi_tiem_m1',

                6 => 'ngay_tiem_m2',
                7 => 'loai_vaccine_m2',
                8 => 'lo_vaccine_m2',
                9 => 'han_su_dung_m2',
                10 => 'don_vi_tiem_m2',

                11 => 'ngay_tiem_m3',
                12 => 'loai_vaccine_m3',
                13 => 'lo_vaccine_m3',
                14 => 'han_su_dung_m3',
                15 => 'don_vi_tiem_m3',

                16 => 'ngay_tiem_m4',
                17 => 'loai_vaccine_m4',
                18 => 'lo_vaccine_m4',
                19 => 'han_su_dung_m4',
                20 => 'don_vi_tiem_m4',
                
            ];
    }

    public static function validator($orders)
    {
        $rules = [
            'form_type' => ["required_with:register_address,term_type", "nullable", Rule::in(array_keys(SchoolHealthInsurance::FORM_TYPE))],
        ];

        return Validator::make($orders, $rules);
    }
}