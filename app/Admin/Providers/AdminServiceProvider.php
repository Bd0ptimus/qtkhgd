<?php

namespace App\Admin\Providers;

use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;

class AdminServiceProvider extends ServiceProvider
{

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'admin.auth' => \App\Admin\Middleware\Authenticate::class,
        'admin.log' => \App\Admin\Middleware\LogOperation::class,
        'admin.permission' => \App\Admin\Middleware\Permission::class,
        'admin.userIdentify' => \App\Admin\Middleware\UserIdentify::class,
        'admin.forceChangePass' => \App\Admin\Middleware\ForceChangePass::class,
        'admin.validatePhone' => \App\Admin\Middleware\ValidatePhone::class,
        'admin.redirectSchoolAgency' => \App\Admin\Middleware\RedirectSchoolAgency::class,
        'admin.selectModule' => \App\Admin\Middleware\SelectModule::class,
        'admin.isDemoAccount' => \App\Admin\Middleware\IsDemoAccount::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'admin' => [
            'admin.auth',
            'admin.log',
            'admin.permission',
            
        ],
        'adminLoggedUser' => [
            'admin.auth',
            'admin.selectModule',
            'admin.log',
            'admin.permission',
            'admin.userIdentify',
            'admin.forceChangePass',
            'admin.validatePhone'
        ]
    ];

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        if (file_exists($routes = base_path('app/Admin/routes.php'))) {
            $this->loadRoutesFrom($routes);
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        $this->registerRouteMiddleware();
    }
    /**
     * Register the route middleware.
     *
     * @return void
     */
    protected function registerRouteMiddleware()
    {
        // register route middleware.
        foreach ($this->routeMiddleware as $key => $middleware) {
            app('router')->aliasMiddleware($key, $middleware);
        }

        // register middleware group.
        foreach ($this->middlewareGroups as $key => $middleware) {
            app('router')->middlewareGroup($key, $middleware);
        }
    }
}
