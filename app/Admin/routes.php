<?php
use Illuminate\Routing\Router;

Route::group([
    'middleware' => ['web', 'admin'],
    'namespace' => 'App\Admin\Controllers',
    'prefix' => config('app.admin_prefix'),
], function (Router $router) {
    foreach (glob(__DIR__ . '/Routes/auth.php') as $filename) {
        require_once $filename;
    }
    $router->get('/', 'HomeController@index')->name('admin.home');
    $router->get('deny', 'HomeController@deny')->name('admin.deny');

    //Language
    $router->get('locale/{code}', function ($code) {
        session(['locale' => $code]);
        return back();
    })->name('admin.locale');

});

Route::group([
    'middleware' => ['web', 'adminLoggedUser', 'admin.isDemoAccount'],
    'namespace' => 'App\Admin\Controllers',
    'prefix' => config('app.admin_prefix'),
], function (Router $router) {
    foreach (glob(__DIR__ . '/Routes/*.php') as $filename) {
        if($filename != 'auth.php') require_once $filename;
    }
    $router->get('/', 'HomeController@index')->name('admin.home');
    $router->get('deny', 'HomeController@deny')->name('admin.deny');

    //Language
    $router->get('locale/{code}', function ($code) {
        session(['locale' => $code]);
        return back();
    })->name('admin.locale');

});
