<?php

namespace App\Admin\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\UserVerification;
use App\Admin\Admin;
use App\Library\Helpers\Sms;
use App\Models\UserDevice;

class ValidatePhone
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
        if(!$user->email && $user->isRole('administrator')) {
            return redirect()->guest($redirectTo);
        } else {
            return $next($request);
        }
    }
}
