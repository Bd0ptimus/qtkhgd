<?php

namespace App\Admin\Models\Imports;

use App\Models\MedicalEquipment;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ImportMedicalEquipment extends BaseImport
{

    protected static $heading = [
        0 => 'ten_trang_thiet_bi',
        1 => 'don_vi',
        2 => 'phan_loai',
        3 => 'chuyen_khoa',
        4 => 'so_luong'
    ];

    protected static $arrFields = [
        'type' => MedicalEquipment::TYPES,
        'specialist' => MedicalEquipment::SPECIALISTS
    ];

    protected static $mappingHeader = [
        'ten_trang_thiet_bi' => 'name',
        'don_vi' => 'unit',
        'phan_loai' => 'type',
        'chuyen_khoa' => 'specialist',
        'so_luong' => 'recommended_quantity'
    ];

    protected static $validator = [
        'rules' => [
            '*.name' => "required",
            '*.unit' => "required",
            '*.recommended_quantity' => "required"
        ],
        'messages' => [
            'required' => 'Giá trị tại dòng :attribute không được bỏ trống',
            'in' => 'Giá trị tại dòng :attribute không đúng',
        ]
    ];
}