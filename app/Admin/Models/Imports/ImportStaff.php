<?php

namespace App\Admin\Models\Imports;

use App\Models\SchoolStaff;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Rules\DateOfBirthRule;

class ImportStaff extends BaseImport
{
    protected static $arrFields = [
        'gender' => SchoolStaff::GENDER,
        'ethnic' => SchoolStaff::ETHNICS,
        'religion' => SchoolStaff::RELIGIONS,
        'nationality' => SchoolStaff::NATIONALITIES,
        'qualification' => SchoolStaff::QUALIFICATIONS,
        'position' => SchoolStaff::POSITIONS,
        'status' => SchoolStaff::STATUS
    ];

    protected static $mappingHeader = [
        'ho_va_ten' => 'fullname',
        'ngay_thang_nam_sinh' => 'dob',
        'gioi_tinh' => 'gender',
        'dan_toc' => 'ethnic',
        'ton_giao' => 'religion',
        'quoc_tich' => 'nationality',
        'dia_chi' => 'address',
        'cmnd' => 'identity_card',
        'so_dien_thoai' => 'phone_number',
        'email' => 'email',
        'trinh_do_chuyen_mon' => 'qualification',
        'chuc_danh' => 'position',
//        'chung_chi_hanh_nghe' => 'professional_certificate',
//        'chuyen_trach' => 'responsible',
        'trang_thai_lam_viec' => 'status'
    ];

    public static function validateFileHeader($heading)
    {
        foreach ($heading as $key => $value) {
            if ($value == null) unset($heading[$key]);
        }

        return $heading == [
                0 => 'ho_va_ten',
                1 => 'ngay_thang_nam_sinh',
                2 => 'gioi_tinh',
                3 => 'dan_toc',
                4 => 'ton_giao',
                5 => 'quoc_tich',
                6 => 'dia_chi',
                7 => 'cmnd',
                8 => 'so_dien_thoai',
                9 => 'email',
                10 => 'trinh_do_chuyen_mon',
                11 => 'chuc_danh',
//                12 => 'chung_chi_hanh_nghe',
//                13 => 'chuyen_trach',
                12 => 'trang_thai_lam_viec'
            ];
    }

    public static function validator($orders)
    {
        $rules = [
            '*.fullname' => "required",
            '*.dob' => ["required", new DateOfBirthRule],
            '*.gender' => ["required", Rule::in(array_keys(SchoolStaff::GENDER))],
            '*.address' => "required",
            '*.position' => ["required", Rule::in(array_keys(SchoolStaff::POSITIONS))],
//            '*.professional_certificate' => ["required_if:*.position,6"],
//            '*.responsible' => ["required_if:*.position,6"],
            '*.status' => ["required", Rule::in(array_keys(SchoolStaff::STATUS))],
        ];
        return Validator::make($orders, $rules, [
            'required' => 'Giá trị tại dòng :attribute không được bỏ trống',
            'in' => 'Giá trị tại dòng :attribute không đúng',
            'required_if' => 'Giá trị tại dòng :attribute không được bỏ trống'
        ]);
    }

    public static function singleValidator($orders)
    {
        $rules = [
            'fullname' => "required",
            'dob' => "required",
            'gender' => ["required", Rule::in(array_keys(SchoolStaff::GENDER))],
            'address' => "required",
            'position' => ["required", Rule::in(array_keys(SchoolStaff::POSITIONS))],
            'professional_certificate' => ["required_if:position,6"],
            'responsible' => ["required_if:position,6"],
            'status' => ["required", Rule::in(array_keys(SchoolStaff::STATUS))],
        ];
        return Validator::make($orders, $rules);
    }

    /* public static function getErrorMessage($errors, $line = null)
    {
        $result = "Vui lòng kiểm tra các lỗi sau: <br>";
        foreach($errors->all() as $error) {
            preg_match_all('!\d+!', $error, $matches);
            $row = intval($matches[0]);
            $realRow = $row + 2;
            $mess = str_replace("{$row}.","{$realRow} - ", $error);
            $mess = static::getRealMessage($mess);
            $result .= $mess."<br>";
        }
        return $result;
    } */

    public static function getRealMessage($mess)
    {
        if (count(static::$mappingHeader) > 0) {
            foreach (static::$mappingHeader as $key => $value) {
                $mess = str_replace($value, $key, $mess);
            }
        }
        return $mess;
    }
}