<?php

namespace App\Http\Middleware;

use App\Admin\Admin;
use App\Models\NotificationAdmin;
use Closure;
use Illuminate\Http\Request;

class VerifyNotification
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $notification = request()->query('notification', null);
        $adminUser = Admin::user();
        if (!empty($adminUser) && !empty($notification)) {
            NotificationAdmin::where('user_id', Admin::user()->id)
                ->where('id', $notification)
                ->where('read', 0)
                ->update(['read' => 1]);
        }

        return $next($request);
    }
}
