<?php

namespace App\Admin\Middleware;

use App\Admin\Admin;
use App\Admin\Models\AdminLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserActivity;

class LogOperation
{

    const  WHITE_LIST_IPS = ['127.0.0.1','123.25.25.5','128.199.104.110','27.71.227.27','14.177.236.7'];
    public static function getRealIP()
    {
        $ip='';
        if ((array_key_exists('HTTP_CF_CONNECTING_IP', $_SERVER) && !empty($_SERVER['HTTP_CF_CONNECTING_IP']))) {
            $ip= $_SERVER["HTTP_CF_CONNECTING_IP"];
        } else if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') > 0) {
                $addr = explode(",", $_SERVER['HTTP_X_FORWARDED_FOR']);
                $ip= trim($addr[0]);
            } else {
                $ip= $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
        } else {
            $ip= $_SERVER['REMOTE_ADDR'];
        }
        if(strlen($ip)>20){
            $ip=substr(0,19);
        }
        return $ip;
    }
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        $routeName = \Request::route()->getName();
        \Log::debug($routeName);
        if(array_key_exists($routeName, UserActivity::ACTIVITIES)){
            $activity = UserActivity::ACTIVITIES[$routeName];
            if ($request->isMethod('get') && preg_match("/create|edit|import/", $routeName))
            {
                $activity = null;
            }
            if($request->isMethod('post')){
                if(UserActivity::getSpecialActivity($routeName)){
                    $activity = UserActivity::getSpecialActivity($routeName);
                }
            }
            if($activity){
                $user_activity = [
                    'school_id' => $request->route('school_id') ?? $request->school_id ?? null,
                    'school_branch_id' => $request->school_branch ?? null,
                    'user_id' => Admin::user()->id,
                    'activity' => $activity,
                    'url' => url()->full(),
                    'data' => json_encode($request->all()),
                    'route_name' => $routeName,
                ];
                try {
                    UserActivity::create($user_activity);
                } catch (\Exception $exception) {
                    dd($exception->getMessage());
                }
            }
        }

        if ($this->shouldLogOperation($request)) {
            $log = [
                'user_id' => Admin::user()->id,
                'path' => substr($request->path(), 0, 255),
                'method' => $request->method(),
                'ip' => self::getRealIP(),
                'user_agent' => $request->header('User-Agent'),
                'input' => json_encode($request->input()),
            ];
            try {
                AdminLog::create($log);
            } catch (\Exception $exception) {
                // pass
            }
        }

        return $next($request);
        
        if(Admin::user()) {
            $realIP = self::getRealIP();
            if(Admin::user()->isAdministrator() && !empty($realIP) && !in_array($realIP, self::WHITE_LIST_IPS)) {
                Auth::guard()->logout();
                $request->session()->invalidate();
                return redirect()->route('admin.login')->withInput()->withErrors([
                    'username' => 'Đăng nhập không được chấp nhận ! '
                ]);
            }
        }
        
        return $next($request);
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function shouldLogOperation(Request $request)
    {
        return sc_config('ADMIN_LOG')
        && !$this->inExceptArray($request)
        && Admin::user();
    }

    /**
     * Determine if the request has a URI that should pass through CSRF verification.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    protected function inExceptArray($request)
    {
        foreach (explode(',', sc_config('ADMIN_LOG_EXP')) as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }
            if ($request->path() == $except) {
                return true;
            }
        }

        return false;
    }
}
