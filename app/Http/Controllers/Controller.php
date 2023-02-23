<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Admin\Helpers\ViewHelper;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Http\Request;
use App\Admin\Admin;
use App\Models\UserActivity;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function renderView($view = null, $data = [], $mergeData = [])
    {
        $factory = app(ViewFactory::class);

        if (func_num_args() === 0) {
            return $factory;
        }
        
        $view = ViewHelper::getView($view);
        return $factory->make($view, $data, $mergeData);
    }

    public function saveActivityLog($activity, $school_id, $school_branch_id = null, $data = null, $routeName = null)
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
