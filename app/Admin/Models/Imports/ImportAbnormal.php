<?php

namespace App\Admin\Models\Imports;

use App\Models\HealthAbnormal;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ImportAbnormal extends BaseImport
{
    protected static $arrFields = [
        'type' => HealthAbnormal::TYPES,
        'diagnosis' => HealthAbnormal::DIAGNOSIS,
        'test_result' => HealthAbnormal::TEST_RESULTS,
        'patient_status' => HealthAbnormal::PATIENT_STATUSES,
        'handle' => HealthAbnormal::HANDLES,
    ];

    protected static $mappingHeader = [
        'ma_hoc_sinh' => 'student_code',
        'ten_hoc_sinh' => 'fullname',
        'ngay' => 'date',
        'chuan_doan_ban_dau' => 'initial_diagnosis',
        'phan_loai' => 'type',
        'chuan_doan' => 'diagnosis',
        'kq_xet_nghiem' => 'test_result',
        'tinh_trang' => 'patient_status',
        'ngay_khoi_phat' => 'begin_date',
        'xu_ly' => 'handle',
        'chuyen_tuyen' => 'move_to',
        'ghi_chu' => 'note',
    ];

    public static function validateFileHeader($heading)
    {
        foreach ($heading as $key => $value) {
            if ($value == null) unset($heading[$key]);
        }

        return $heading == [
                0 => 'ma_hoc_sinh',
                1 => 'ten_hoc_sinh',
                2 => 'ngay',
                3 => 'chuan_doan_ban_dau',
                4 => 'phan_loai',
                5 => 'chuan_doan',
                6 => 'kq_xet_nghiem',
                7 => 'tinh_trang',
                8 => 'ngay_khoi_phat',
                9 => 'xu_ly',
                10 => 'chuyen_tuyen',
                11 => 'ghi_chu'
            ];
    }

    public static function validator($orders)
    {
        $rules = [
            'student_code' => "required",
            'date' => "required",
            'initial_diagnosis' => "required_with:type,diagnosis,handle",
            'type' => ["required_with:initial_diagnosis,diagnosis,handle", "nullable", Rule::in(array_keys(HealthAbnormal::TYPES))],
            'diagnosis' => ["required_with:initial_diagnosis,type,handle", "nullable", Rule::in(array_keys(HealthAbnormal::DIAGNOSIS))],
            'test_result' => ["nullable", Rule::in(array_keys(HealthAbnormal::TEST_RESULTS))],
            'patient_status' => ["nullable", Rule::in(array_keys(HealthAbnormal::PATIENT_STATUSES))],
            'handle' => ["required_with:initial_diagnosis,type,diagnosis", "nullable", Rule::in(array_keys(HealthAbnormal::HANDLES))],
        ];

        return Validator::make($orders, $rules);
    }
}