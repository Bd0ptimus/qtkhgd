<?php

namespace App\Admin\Models\Imports;

use App\Models\SchoolClass;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;

class ImportClass extends BaseImport
{
    use Importable;

    protected static $arrFields = [
        'grade' => SchoolClass::GRADES,
    ];

    protected static $mappingHeader = [
        'khoi' => 'grade',
        'lop' => 'class_name',
    ];

    public static function validateFileHeader($heading)
    {
        foreach ($heading as $key => $value) {
            if ($value == null) unset($heading[$key]);
        }

        return $heading == [
                0 => 'khoi',
                1 => 'lop',
            ];
    }

    public static function validator($orders, $ignoreId = null)
    {
        $rules = [
            'school_id' => "required",
            'grade' => ["required",
                Rule::in(array_keys(SchoolClass::GRADES))
            ],
            'class_name' => [
                "required",
                Rule::unique('class', 'class_name')->where(function ($query) use ($orders) {
                    return $query->where('school_id', $orders['school_id']);
                })->ignore($ignoreId)
            ]
        ];

        $messages = [
            'school_id.required' => trans('validation.required', ['attribute' => 'điểm trường']),
            'grade.required' => trans('validation.required', ['attribute' => 'khối']),
            'class_name.required' => trans('validation.required', ['attribute' => 'lớp']),
            'class_name.unique' => trans('validation.unique', ['attribute' => 'lớp'])
        ];

        return Validator::make($orders, $rules, $messages);
    }
}