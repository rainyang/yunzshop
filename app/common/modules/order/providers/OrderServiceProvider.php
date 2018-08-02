<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/8/2
 * Time: 上午11:52
 */

namespace app\common\modules\order\providers;

use app\frontend\modules\order\services\OrderManager;
use Illuminate\Support\ServiceProvider;

class OrderServiceProvider extends ServiceProvider
{
    public function boot(){
        $this->app->singleton('OrderManager',function(){
            return new OrderManager();
        });

    }
}