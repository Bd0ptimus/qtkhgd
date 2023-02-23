<?php

namespace App\Admin\Models\Imports;

use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;

abstract class BaseImportMergeHeading implements WithChunkReading, WithStartRow
{
    use Importable;

    protected static $arrFields = [
    ];

    protected static $mappingFields = [
    ];

    protected static $validator = [
        'rules' => [],
        'messages' => []
    ];

    public static function filterData($rows)
    {
        $newRows = [];
        foreach ($rows as $index => $row) {
            if (!array_filter($row)) {
                unset($rows[$index]);
            } else {
                $newRow = [];
                foreach ($row as $cellIndex => $cell) {
                    $field = static::$mappingFields[$cellIndex];
                    if (!empty($field)) {
                        $arrFields = static::$arrFields;
                        if (array_key_exists($field, $arrFields) && $arrFields[$field] !== null) {
                            $newRow[$field] = array_search($cell, $arrFields[$field]);
                        } else {
                            $newRow[$field] = $cell;
                        }
                    }
                }

                $newRows[] = $newRow;
            }
        }
        return $newRows;
    }

    public static function validator($data)
    {
        return Validator::make($data, static::$validator['rules'], static::$validator['messages']);
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}