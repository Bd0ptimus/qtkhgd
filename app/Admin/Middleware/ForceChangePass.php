<?php

namespace App\Admin\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\UserVerification;
use App\Admin\Admin;
use App\Library\Helpers\Sms;
use App\Models\UserDevice;
use App\Models\AdminConfig;

class ForceChangePass
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
        $user = Admin::user();
        $redirectTo = route('admin.setting');
        if($user->force_change_pass == true) {
            return redirect()->guest($redirectTo);
        } else {
            return $next($request);
        }
    }
}
