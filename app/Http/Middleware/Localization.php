<?php

namespace App\Http\Middleware;

use App\Models\ShopLanguage;
use Closure;
use Session;

class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        app()->setLocale(config('app.locale'));
//End language
        return $next($request);
    }
}
