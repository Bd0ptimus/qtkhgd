<?php

namespace App\Admin\Models\Imports;

use App\Models\HealthChannel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ImportLocation extends BaseImport
{

    protected static $heading = [
        0 => "tinh_thanh_pho",
        1 => "ma_tp",
        2 => "quan_huyen",
        3 => "ma_qh",
        4 => "phuong_xa",
        5 => "ma_px",
        6 => "cap",
        7 => "ten_tieng_anh"
    ];
}