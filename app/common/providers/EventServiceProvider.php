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
        ]
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
