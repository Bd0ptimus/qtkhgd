<?php

namespace App\Admin\Models\Imports;

use App\Models\StudentHealthIndex;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ImportPeriodicalCheck extends BaseImportMergeHeading
{
    protected static $arrFields = [];

    protected static $mappingFields = [
        'student_code',
        'fullname',
        'month',
        'weight',
        'height',
        'systolic_blood_pressure',
        'diastolic_blood_pressure',
        'heart_rate',
        'right_without_glass_eyesight',
        'left_without_glass_eyesight',
        'right_with_glass_eyesight',
        'left_with_glass_eyesight',
    ];

    public static function validator($item)
    {
        $rules = [
            'student_code' => "required",
            'month' => "required",
            'right_without_glass_eyesight' => ["nullable", Rule::in(StudentHealthIndex::EYE_SIGHT_OPTIONS)],
            'left_without_glass_eyesight' => ["nullable", Rule::in(StudentHealthIndex::EYE_SIGHT_OPTIONS)],
            'right_with_glass_eyesight' => ["nullable", Rule::in(StudentHealthIndex::EYE_SIGHT_OPTIONS)],
            'left_with_glass_eyesight' => ["nullable", Rule::in(StudentHealthIndex::EYE_SIGHT_OPTIONS)],
        ];

        return Validator::make($item, $rules);
    }

    public function startRow(): int
    {
        return 3;
    }
}