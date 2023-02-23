<?php

namespace App\Admin\Models\Imports;

use App\Models\Medicine;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class ImportMedicine extends BaseImport
{
    protected static $heading = [
        0 => 'danh_muc',
        1 => 'danh_muc_con',
        2 => 'ten_thuoc',
        3 => 'duong_dung_ham_luong_dang_bao_che',
        4 => 'loai_thuoc',
        5 => 'ghi_chu',
        6 => 'yc_co_bac_si'
    ];

    protected static $arrFields = [
        'is_basic' => Medicine::IS_BASIC,
        'required_doctor' => Medicine::REQUIRED_DOCTOR
    ];

    protected static $mappingHeader = [
        'danh_muc' => 'category',
        'danh_muc_con' => 'sub_category',
        'ten_thuoc' => 'name',
        'duong_dung_ham_luong_dang_bao_che' => 'medicine_info',
        'loai_thuoc' => 'is_basic',
        'ghi_chu' => 'note',
        'yc_co_bac_si' => 'required_doctor'
    ];

    protected static $validator = [
        'rules' => [
            '*.category' => "required",
            '*.name' => "required",
            '*.medicine_info' => "required",
            '*.required_doctor' => "required|in:0,1",
            '*.is_basic' => "required|in:1,2"
        ],
        'messages' => [
            'required' => 'Giá trị tại dòng :attribute không được bỏ trống',
            'in' => 'Giá trị tại dòng :attribute không đúng',
        ]
    ];
}