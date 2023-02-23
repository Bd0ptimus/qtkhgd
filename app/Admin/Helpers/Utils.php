<?php // Code within app\Helpers\Helper.php

namespace App\Admin\Helpers;

use Exception;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Storage;

class Utils
{
    public static function formatDate($date, $format = 'Y-m-d')
    {
        try {
            if (is_numeric($date)) {
                //Excel date serial format
                $UNIX_DATE = ($date - 25569) * 86400;
                $result = gmdate($format, $UNIX_DATE);
            } else {
                // Excel format
                $result = Carbon::createFromFormat('d/m/Y', $date)->format($format);
            }

            return $result;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function datesOfWeek($date)
    {
        $from_date = Carbon::parse($date)->startOfWeek(Carbon::MONDAY)->format('Y-m-d');
        $to_date = Carbon::parse($from_date)->addDays(5)->format('Y-m-d');
        
        $period = CarbonPeriod::create($from_date, $to_date);
        $days_of_week = [];
        foreach ($period as $date) {
            $days_of_week[] = $date->format('Y-m-d');
        }

        return $days_of_week;
    }

    public static function previousYear()
    {
        $selectedYear = \App\Admin\Helpers\ListHelper::listYear()[0];
        return ($selectedYear - 1) ." - ". $selectedYear;
    }

    public function takeSchoolLevel($data){
        if(in_array($data,[1,2,3,4,5])){
            return 1;
        }elseif(in_array($data,[6,7,8,9])){
            return 2;
        }else if(in_array($data,[10,11,12])){
            return 3;
        }
        return 6;
    }

    public static function takeSchoolGradesByLevel($level){
        $grades=[];
        if(!isset($level)) return [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18];
        switch($level) {
            case 1:
                $grades = [1,2,3,4,5];
                break;
            case 2:
                $grades = [6,7,8,9];
                break;
            case 4:
                $grades = [1,2,3,4,5,6,7,8,9];
                break;
            case 3:
                $grades = [10,11,12];
                break;
            case 5:
                $grades = [6,7,8,9,10,11,12];
                break;
            case 6:
                $grades = [13,14,15,16,17,18];
                break;
        }
        return $grades;
    }

    public static function getLinkS3($path, $minutes = 5) {
        return Storage::disk('s3')->temporaryUrl($path, now()->addMinutes($minutes));
    }
}