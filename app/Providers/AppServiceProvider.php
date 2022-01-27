<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class AppServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerRouteMiddleware();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (!file_exists(public_path('install.php')) && file_exists(base_path('.env'))) {
            foreach (glob(app_path() . '/Helpers/*.php') as $filename) {
                require_once $filename;
            }
            foreach (glob(app_path() . '/Plugins/*/*/Provider.php') as $filename) {
                require_once $filename;
            }
            //Route Api
            
            if (file_exists($routes = base_path('routes/api.php'))) {
                $this->loadRoutesFromPath($routes,'api','api');
            }

            $storeId = LC_ID_ROOT;
            
            config(['app.storeId' => $storeId]);
        }
    }

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'localization'     => \App\Http\Middleware\Front\Localization::class,
        'email.verify'     => \App\Http\Middleware\Front\EmailIsVerified::class,
        'currency'         => \App\Http\Middleware\Front\Currency::class,
        'checkdomain'      => \App\Http\Middleware\Front\CheckDomain::class,
        //Admin
        'admin.log'        => \App\Http\Middleware\Admin\LogOperation::class,
        'admin.permission' => \App\Http\Middleware\Admin\PermissionMiddleware::class,
        'admin.storeId'    => \App\Http\Middleware\Admin\AdminStoreId::class,
        'admin.theme'      => \App\Http\Middleware\Admin\AdminTheme::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'admin' => [
            'admin.permission',
            'admin.log',
            'admin.storeId',
            'admin.theme',
        ],
        'front' => [
            'localization',
            'currency',
            'checkdomain',
        ]
    ];
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
    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function loadRoutesFromPath($routes,$prefix,$middleware)
    {
        Route::prefix($prefix)
            ->middleware($middleware)
            ->namespace($this->namespace)
            ->group($routes);
    }
}
