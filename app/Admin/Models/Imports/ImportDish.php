<?php

namespace App\Admin\Models\Imports;

use App\Models\Dish;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class ImportDish extends BaseImport
{
    protected static $heading = [
        0 => 'loai',
        1 => 'vung_mien',
        2 => 'ten_mon_an',
        3 => 'cach_che_bien'
    ];

    protected static $arrFields = [
        'category' => Dish::CATEGORIES,
        'region' => Dish::REGIONS
    ];

    protected static $mappingHeader = [
        'loai' => 'category',
        'vung_mien' => 'region',
        'ten_mon_an' => 'name',
        'cach_che_bien' => 'processing'
    ];


    protected static $validator = [
        'rules' => [
            '*.category' => "required|in:1,2,3,4,5,6,7,8",
            '*.region' => "required|in:1,2,3",
            '*.name' => "required"
        ],
        'messages' => [
            'required' => 'Giá trị tại dòng :attribute không được bỏ trống',
            'in' => 'Giá trị tại dòng :attribute không đúng',
        ]
    ];
}