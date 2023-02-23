<?php

namespace App\Admin\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\UserVerification;
use App\Admin\Admin;
use App\Library\Helpers\Sms;
use App\Models\UserDevice;
use App\Models\AdminConfig;

class SelectModule
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $workingModule = session()->get('workingModule');
        $redirectTo = route('admin.select_module');
        if( $workingModule ) {
            switch($workingModule) {
                case 'teaching_management': 
                    return $next($request);
                default:
                    return redirect()->guest($redirectTo);
            }
        } else {
            return redirect()->guest($redirectTo);
        }
       
    }
}
