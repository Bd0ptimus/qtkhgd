<?php

namespace App\Admin\Models\Imports;

use App\Models\SchoolHealthInsurance;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ImportInsurance extends BaseImport
{
    protected static $arrFields = [
        'form_type' => SchoolHealthInsurance::FORM_TYPE,
        'term_type' => SchoolHealthInsurance::TERM_TYPE,
    ];

    protected static $mappingHeader = [
        //'ma_hoc_sinh' => 'student_code',
        'ho_ten' => 'fullname',
        'hinh_thuc_tham_gia_bao_hiem' => 'form_type',
        'noi_dang_ky_kham_chua_benh' => 'register_address',
        'thoi_han_dong_bao_hiem' => 'term_type',
        'thoi_han_su_dung_tu_ngay' => 'start_at',
        'thoi_han_su_dung_den_ngay' => 'end_at',
        'so_the_bao_hiem' => 'insurance_code',
        'so_tien_phai_nop' => 'cash',
        'ngay_ban_giao_the' => 'handover_at',
    ];

    public static function validateFileHeader($heading)
    {
        foreach ($heading as $key => $value) {
            if ($value == null) unset($heading[$key]);
        }


        return $heading == [
                //0 => 'ma_hoc_sinh',
                0 => 'ho_ten',
                1 => 'hinh_thuc_tham_gia_bao_hiem',
                2 => 'noi_dang_ky_kham_chua_benh',
                3 => 'thoi_han_dong_bao_hiem',
                4 => 'thoi_han_su_dung_tu_ngay',
                5 => 'thoi_han_su_dung_den_ngay',
                6 => 'so_the_bao_hiem',
                7 => 'so_tien_phai_nop',
                8 => 'ngay_ban_giao_the'
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