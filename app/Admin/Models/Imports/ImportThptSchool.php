<?php

namespace App\Admin\Models\Imports;
use App\Models\School;

class ImportThptSchool extends BaseImport
{

    protected static $heading = [
        0 => "xa_phuong",
        1 => "ten_truong",
        2 => "email",
        3 => "so_dien_thoai",
        4 => "dia_chi",
        5 => "cap"
    ];

    protected static $arrFields = [
        "school_type" => School::SCHOOL_THPT_TYPES
    ];

    protected static $mappingHeader = [
        "xa_phuong" => "ward",
        "ten_truong" => "school_name",
        "email" => "school_email",
        "so_dien_thoai" => "school_phone",
        "dia_chi" => "school_address",
        "cap" => "school_type",
    ];

    protected static $validator = [
        'rules' => [
            '*.ward' => "required",
            '*.school_name' => "required",
            '*.school_address' => "required",
            '*.school_type' => "required|in:3,5",
        ],
        'messages' => [
            'required' => 'Giá trị tại dòng :attribute không được bỏ trống',
            'in' => 'Giá trị tại dòng :attribute không đúng',
        ]
    ];
}