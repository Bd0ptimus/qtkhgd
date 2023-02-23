<?php // Code within app\Helpers\Helper.php

namespace App\Admin\Helpers;
use Illuminate\Support\Facades\Log;


class ListHelper
{
    public static function listMonth($schoolYear = null)
    {
        $yearData = ListHelper::listYear($schoolYear);
        $firstYear = $yearData[0];
        $secondYear = $yearData[1];

        return [
            "08-$firstYear",
            "09-$firstYear",
            "10-$firstYear",
            "11-$firstYear",
            "12-$firstYear",
            "01-$secondYear",
            "02-$secondYear",
            "03-$secondYear",
            "04-$secondYear",
            "05-$secondYear",
            "06-$secondYear"
        ];
    }

    public static function listYear($schoolYear = null)
    {
        if (empty($schoolYear)) {
            $year = session()->get('year');
            if($year) {
                $firstYear = $year;
                $secondYear = $year + 1;
            } else {
                $currentMonth = date('m', time());
                $currentYear = date('Y', time());
                if ($currentMonth >= 7) {
                    $firstYear = $currentYear;
                    $secondYear = $currentYear + 1;
                } else {
                    $firstYear = $currentYear - 1;
                    $secondYear = $currentYear;
                }
            }
        } else {
            $years = explode("-", $schoolYear);
            $firstYear = $years[0];
            $secondYear = $years[1];
        }

        return [$firstYear, $secondYear];
    }

    public static function getDefaultSchoolYear() {
        $currentMonth = date('m', time());
        $currentYear = date('Y', time());
        if ($currentMonth >= 7) {
            $firstYear = $currentYear;
            $secondYear = $currentYear + 1;
        } else {
            $firstYear = $currentYear - 1;
            $secondYear = $currentYear;
        }
        return [$firstYear, $secondYear];
    }

    public static function findObjectInArray($array, $attribute, $value, $groupBy=null) {
        $arrayOverlap = array();
        foreach($array as $index => $item) {
            if(!in_array($item->$groupBy,$arrayOverlap )){
                array_push($arrayOverlap, $item->$groupBy);
            }
            if($item->$attribute == $value) return [
                'index' => sizeof($arrayOverlap)-1,
                'object' => $item
            ];
        }
        return [
            'index' => null,
            'object' => null
        ];
    }


}