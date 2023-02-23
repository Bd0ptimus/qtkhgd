<?php

namespace App\Admin\Models\Imports;

use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Validator;

abstract class BaseImport implements WithHeadingRow, WithChunkReading
{
    use Importable;

    /**
     * @param array $row
     *
     * @return User|null
     */

    protected static $heading = [];

    protected static $arrFields = [
    ];

    protected static $mappingHeader = [
    ];

    protected static $fieldHasDefaults = [];

    protected static $validator = [
        'rules' => [],
        'messages' => []
    ];

    public static function validateFileHeader($heading)
    {

        foreach ($heading as $key => $value) {
            if ($value == null) unset($heading[$key]);
        }
        return $heading == static::$heading;
    }

    public static function mappingKey($rows)
    {
        $newRows = [];
        foreach ($rows as $index => $row) {
            $data = [];
            foreach ($row as $key => $value) {
                if (!empty($key)) {
                    $newKey = static::$mappingHeader[$key];
                    $data[$newKey] = $value;
                }
            }
            array_push($newRows, $data);
        }
        return $newRows;
    }

    public static function filterData($rows)
    {
        $newRows = [];
        foreach ($rows as $index => $row) {
            if (!array_filter($row)) {
                unset($rows[$index]);
            } else {
                // Replace string value by number value to save database
                foreach (static::$arrFields as $key => $field) {
                    if (array_key_exists($key, $row) && $row[$key] !== null) {
                        $row[$key] = array_search($row[$key], $field);
                    }
                }

                foreach (static::$fieldHasDefaults as $fieldname => $defaultValue) {
                    if ($row[$fieldname] === null) $row[$fieldname] = $defaultValue;
                }

                $newRows[] = $row;
            }
        }
        return $newRows;
    }

    public static function validator($data)
    {
        return Validator::make($data, static::$validator['rules'], static::$validator['messages']);
    }

    public static function getErrorMessage($errors, $line = null)
    {
        $result = "Vui lòng kiểm tra các lỗi" . (is_null($line) ? " " : " tại dòng $line ") . "sau: <br>";
        foreach ($errors->all() as $error) {
            preg_match_all('!\d+!', $error, $matches);
            try {
                $row = intval($matches[0][0]);
            } catch (\Exception $e) {
                $row = intval($matches[0]);
            }
            $realRow = $row + 2;
            $mess = str_replace("{$row}.", "{$realRow} - ", $error);
            $mess = static::getRealMessage($mess);
            $result .= $mess . "<br>";
        }
        return $result;
    }

    public static function getRealMessage($mess)
    {
        if (count(static::$mappingHeader) > 0) {
            foreach (static::$mappingHeader as $key => $value) {
                $mess = str_replace($value, $key, $mess);
            }
        }
        return $mess;
    }

    public function model(array $row)
    {
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}