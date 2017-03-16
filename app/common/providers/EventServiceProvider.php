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
        //todo 需要转移文件夹(已注册)
        \app\common\events\order\OrderGoodsDiscountWasCalculated::class => [ //商品优惠计算
            \app\frontend\modules\goods\listeners\MemberLevelGoodsDiscount::class, //用户等级优惠
        ],
        \app\common\events\order\OrderDiscountWasCalculated::class => [ //订单优惠计算
            \app\frontend\modules\order\listeners\discount\testOrderDiscount::class, //立减优惠
        ],
        \app\common\events\order\OrderGoodsDispatchWasCalculated::class => [ //商品运费统计
            \app\frontend\modules\goods\listeners\UnifyGoodsDispatch::class, //统一运费
        ],
        \app\common\events\order\OrderDispatchWasCalculated::class => [ //订单邮费计算
            \app\frontend\modules\order\listeners\dispatch\prices\UnifyOrderDispatchPrice::class, //统一运费
        ],


        //todo 需要改成以下格式(现在未注册)
        'app\frontend\modules\goods\events\OrderGoodsDiscountWasCalculated'=>[//订单商品计算优惠时
            'app\frontend\modules\goods\listeners\MemberLevelGoodsDiscount', //用户等级优惠
        ],
    ];
    protected $subscribe = [
        \app\frontend\modules\order\listeners\dispatch\types\Express::class,
        \app\frontend\modules\member\listeners\Level::class,

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
