<?php

namespace App\Admin\Middleware;

use App\Admin\Admin;
use Closure;


class IsDemoAccount
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
        if(Admin::user()->is_demo_account == 1 && ($request->isMethod("post") || $request->isMethod("put") || $request->isMethod("delete"))) {
            return redirect()->back()->with('error', 'Tài khoản của bạn chưa được kích hoạt tính năng này');
        }
        return $next($request);
    }
}
