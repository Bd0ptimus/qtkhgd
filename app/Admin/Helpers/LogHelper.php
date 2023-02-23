<?php // Code within app\Helpers\Helper.php

namespace App\Admin\Helpers;
use App\Admin\Admin;
use App\Models\UserActivity;

class LogHelper
{
   public static function saveActivityLog($activity, $school_id, $school_branch_id = null, $data = null, $routeName = null)
   {
       if($activity){
           $routeName = $routeName ?? \Request::route()->getName();
           $user_activity = [
               'school_id' => $school_id,
               'school_branch_id' => $school_branch_id,
               'user_id' => Admin::user()->id,
               'activity' => $activity,
               'url' => url()->full(),
               'data' => $data,
               'route_name' => $routeName,
           ];
           try {
               UserActivity::create($user_activity);
           } catch (\Exception $exception) {
               dd($exception->getMessage());
           }
       }
   
   }
}