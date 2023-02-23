<?php

namespace App\Admin\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\UserVerification;
use App\Admin\Admin;
use App\Library\Helpers\Sms;
use App\Models\UserDevice;
use App\Models\AdminConfig;

class UserIdentify
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
        //dd($_COOKIE);
        return $next($request);
        $user = Admin::user();
        if($user->status == 0) {
            echo "Tài khoản của bạn bị tạm khoá";
            Auth::guard('admin')->logout();
            $request->session()->invalidate();
            exit;
        }

        $redirectTo = route('admin.identify');
        if(isset($_COOKIE[$user->id]['user_identify_token']) ) {
            $user_identify_token = $_COOKIE[$user->id]['user_identify_token'];
            $user_device = UserDevice::where('user_id', $user->id)->where('identify_token', $user_identify_token)->first();
            if($user_device) {
                return $next($request);
            } else {
                setcookie("{$user->id}[user_identify_token]", sha1(time()), time() - 1 , '/');
                return $next($request);
            }
        } else {
            return redirect()->guest($redirectTo);
        }
    }
}
