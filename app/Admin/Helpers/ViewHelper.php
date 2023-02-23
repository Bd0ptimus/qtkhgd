<?php // Code within app\Helpers\Helper.php

namespace App\Admin\Helpers;

use Exception;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Admin\Admin;

class ViewHelper
{
    public static function getView($viewName) {
        $routeName = \Request::route()->getName();
        return $viewName;
        
        if (Admin::user()->inRoles([ROLE_ADMIN, ROLE_CM])) {
            return $viewName;
        } else if (strpos($routeName, 'tasks') !== false) {
            return $viewName;
        }

        return \str_replace('admin.', 'admin.view_only.', $viewName);
    }
}