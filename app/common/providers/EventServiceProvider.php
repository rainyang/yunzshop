<?php

namespace app\common\providers;


use app\frontend\modules\discount\listeners\MemberLevelGoodsDiscount;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \app\common\events\discount\OrderGoodsDiscountWasCalculated::class => [ //商品优惠计算
            \app\frontend\modules\discount\listeners\MemberLevelGoodsDiscount::class, //用户等级优惠
        ],
        \app\common\events\discount\OrderDiscountWasCalculated::class => [ //订单优惠计算
            \app\frontend\modules\order\listeners\discount\testOrderDiscount::class, //立减优惠
        ],
        \app\common\events\dispatch\OrderGoodsDispatchWasCalculated::class => [ //商品运费统计
            \app\frontend\modules\dispatch\listeners\prices\UnifyGoodsDispatch::class, //统一运费
        ],
        \app\common\events\dispatch\OrderDispatchWasCalculated::class => [ //订单邮费计算
            \app\frontend\modules\dispatch\listeners\prices\UnifyOrderDispatchPrice::class, //统一运费
        ],

    ];
    protected $subscribe = [
        \app\frontend\modules\dispatch\listeners\types\Express::class,
        \app\frontend\modules\member\listeners\Level::class,
        \app\common\listeners\order\OrderTestListener::class,
        \app\common\listeners\goods\GoodsTestListener::class,
        MemberLevelGoodsDiscount::class


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
