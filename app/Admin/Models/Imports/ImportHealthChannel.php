<?php

namespace App\Admin\Models\Imports;

use App\Models\HealthChannel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ImportHealthChannel extends BaseImport
{

    protected static $heading = [
        0 => 'loai_phan_kenh',
        1 => 'gioi_tinh',
        2 => 'so_thang_tuoi',
        3 => 'chieu_cao',
        4 => 'sd3neg',
        5 => 'sd2neg',
        6 => 'sd1neg',
        7 => 'binh_thuong',
        8 => 'sd1',
        9 => 'sd2',
        10 => 'sd3'
    ];

    protected static $arrFields = [
        'gender' => HealthChannel::GENDERS,
        'type' => HealthChannel ::TYPES
    ];

    protected static $mappingHeader = [
        'chieu_cao' => 'height',
        'gioi_tinh' => 'gender',
        'so_thang_tuoi' => 'month',
        'loai_phan_kenh' => 'type',
        'sd3neg' => 'sd3neg',
        'sd2neg' => 'sd2neg',
        'sd1neg' => 'sd1neg',
        'binh_thuong' => 'normal',
        'sd1' => 'sd1',
        'sd2' => 'sd2',
        'sd3' => 'sd3'
    ];

    protected static $validator = [
        'rules' => [
            '*.gender' => "required|in:1,2",
            '*.month' => "min:0",
            '*.height' => "min:0",
            '*.type' => "required|numeric|in:1,2,3,4,5",
            '*.sd3neg' => "required",
            '*.sd2neg' => "required",
            '*.sd1neg' => "required",
            '*.normal' => "required",
            '*.sd1' => "required",
            '*.sd3' => "required",
            '*.sd3' => "required",
        ],
        'messages' => [
            'required' => 'Giá trị tại dòng :attribute không được bỏ trống',
            'in' => 'Giá trị tại dòng :attribute không đúng'
        ]
    ];
}