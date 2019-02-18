<?php

namespace app\common\providers;

use app\common\services\Check;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'app';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        if (env('APP_Framework') == 'platform') {
            $this->mapPlatformRoutes();
            $this->mapShopRoutes();
        } else {
            // $this->mapApiRoutes();
            $this->mapWebRoutes();
        }
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::group([
            'middleware' => ['web'],
            'namespace' => $this->namespace,
        ], function ($router) {
            //strpos(request()->get('route'),'setting.key') !== 0 && Check::app();
            require base_path('routes/web.php');
        });
    }

    protected function mapApiRoutes()
    {
        Route::middleware('api')->prefix('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }

    protected function mapPlatformRoutes()
    {
        Route::group([
            'prefix' => 'admin',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/admin.php');
        });
    }

    protected function mapShopRoutes()
    {
        Route::group([
            'prefix' => 'shop',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/shop.php');
        });
    }

}
