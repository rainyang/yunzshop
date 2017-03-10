<?php

namespace app\common\providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'app\common\events\TestFailEvent' => [ //事件类
            'app\common\listeners\EventListener', //侦听类1
            'app\common\listeners\EventListenerOther', //侦听类2
        ],
        'app\common\events\TestGoodsEvent'=>[
            'app\common\listeners\EventListenerGoods', //侦听类1
            'app\backend\modules\goods\controllers\BrandController', //侦听类1
        ],
        'app\common\events\OrderCreatedEvent'=>[
            'app\common\listeners\EventListener', //侦听类1
            'app\common\listeners\EventListenerOther', //侦听类2
        ],
        'app\common\events\OrderGoodsPriceWasCalculated'=>[//订单添加商品后
            'app\frontend\modules\goods\services\models\RealGoodsDispatch', //订单商品运费计算
        ],
        'app\common\events\OrderPriceWasCalculated'=>[//订单价格计算后
            'app\frontend\modules\order\services\models\OrderDispatch', //订单运费计算
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
