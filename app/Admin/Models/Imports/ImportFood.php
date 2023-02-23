<?php

namespace App\Admin\Models\Imports;

use App\Models\Food;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class ImportFood extends BaseImport
{
    protected static $heading = [
        0 => 'category',
        1 => 'name',
        2 => 'ty_le_thai',
        3 => 'khong_an',
        4 => 'dong_vat',
        5 => 'kho_hang',
        6 => 'ty_le_calo',
        7 => 'tphh_nuoc',
        8 => 'tphh_protit',
        9 => 'tphh_lipit',
        10 => 'tphh_gluxit',
        11 => 'tphh_cellulose',
        12 => 'tphh_tro',
        13 => 'vitamin_caroten',
        14 => 'vitamin_a',
        15 => 'vitamin_b1',
        16 => 'vitamin_b2',
        17 => 'vitamin_c',
        18 => 'vitamin_pp',
        19 => 'khoang_calci',
        20 => 'khoang_photpho',
        21 => 'khoang_sat',
        22 => 'usual',
        23 => 'goi_y_nha_tre',
        24 => 'goi_y_mau_giao',
        25 => 'type',
        26 => 'source'
    ];

    protected static $arrFields = [
        'category' => Food::CATEGORIES,
        'khong_an' => Food::TRUE_OR_FALSE,
        'dong_vat' => Food::TRUE_OR_FALSE,
        'kho_hang' => Food::TRUE_OR_FALSE,
        'usual' => Food::TRUE_OR_FALSE
    ];


    protected static $validator = [
        'rules' => [
            '*.category' => "required|in:1,2,3,4,5,6,7,8,9,10,11",
            '*.name' => "required",
            '*.ty_le_thai' => "required",
            '*.ty_le_calo' => "required",
            '*.khong_an' => "nullable|in:0,1",
            '*.dong_vat' => "nullable|in:0,1",
            '*.kho_hang' => "nullable|in:0,1",
            '*.type' => "nullable|in:1,2",
            '*.source' => "nullable|in:1,2,3",
        ],
        'messages' => [
            'required' => 'Giá trị tại dòng :attribute không được bỏ trống',
            'in' => 'Giá trị tại dòng :attribute không đúng',
        ]
    ];
}