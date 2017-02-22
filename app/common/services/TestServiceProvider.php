<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 22/02/2017
 * Time: 01:37
 */

namespace app\common\services;

use Illuminate\Support\ServiceProvider;
use app\common\TestService;

class TestServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     * @author LaravelAcademy.org
     */
    public function register()
    {
        //使用singleton绑定单例
        $this->app->singleton('test',function(){
            return new TestService();
        });

        //使用bind绑定实例到接口以便依赖注入
        $this->app->bind('app\common\services\TestContract',function(){
            return new TestService();
        });
    }
}
{

}