<?php

namespace App\Admin\Services;

use App\Models\SchoolClass;

class CommonService
{
    public function getGradeBySchoolType($schoolType)
    {
        $grades = collect(SchoolClass::GRADES);

        switch ($schoolType) {
            case SCHOOL_TH:
                $grades = $grades->filter(function ($value, $key) {
                    return in_array($key, [1, 2, 3, 4, 5]);
                });
                break;
            case SCHOOL_THCS:
                $grades = $grades->filter(function ($value, $key) {
                    return in_array($key, [6, 7, 8, 9]);
                });
                break;
            case SCHOOL_GDTX:
            case SCHOOL_THPT:
                $grades = $grades->filter(function ($value, $key) {
                    return in_array($key, [10, 11, 12]);
                });
                break;
            case SCHOOL_MN:
                $grades = $grades->filter(function ($value, $key) {
                    return in_array($key, [13, 14, 15, 16, 17, 18]);
                });
                break;
            case SCHOOL_LC12:
                $grades = $grades->filter(function ($value, $key) {
                    return in_array($key, [1, 2, 3, 4, 5, 6, 7, 8, 9]);
                });
                break;
            case SCHOOL_LC23:
                $grades = $grades->filter(function ($value, $key) {
                    return in_array($key, [6, 7, 8, 9, 10, 11, 12]);
                });
                break;
        }

        return $grades->toArray();
    }
}