<?php

namespace app\common\providers;


use app\common\events\PayLog;
use app\common\events\WechatProcessor;
use app\common\listeners\PayLogListener;
use app\common\listeners\point\PointLisrener;
use app\common\listeners\WechatProcessorListener;
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
            \app\frontend\modules\order\listeners\discount\TestOrderDiscount::class, //立减优惠
        ],
        \app\common\events\dispatch\OrderGoodsDispatchWasCalculated::class => [ //商品运费统计
            \app\frontend\modules\dispatch\listeners\prices\UnifyGoodsDispatch::class, //统一运费
        ],
        \app\common\events\dispatch\OrderDispatchWasCalculated::class => [ //订单邮费计算
            \app\frontend\modules\dispatch\listeners\prices\UnifyOrderDispatchPrice::class, //统一运费
        ],
        PayLog::class => [ //支付日志请求
            PayLogListener::class //保存支付参数
        ],
        \app\common\events\member\BecomeAgent::class => [ //会员成为下线
          \app\common\listeners\member\BecomeAgentListener::class
        ],
        \app\common\events\order\AfterOrderCreatedEvent::class => [ //下单成功后调用会员成为下线事件
            \app\common\listeners\member\AfterOrderCreatedListener::class
        ],
        //微信接口回调触发事件进程
        WechatProcessor::class => [
            WechatProcessorListener::class//示例监听类
        ]

    ];
    protected $subscribe = [
        \app\frontend\modules\dispatch\listeners\types\Express::class,
        \app\frontend\modules\member\listeners\Level::class,
        \app\common\listeners\order\OrderTestListener::class,
        \app\common\listeners\goods\GoodsTestListener::class,
        \app\frontend\modules\coupon\listeners\CouponDiscount::class,
        \app\frontend\modules\discount\listeners\MemberLevelGoodsDiscount::class,
        PointLisrener::class

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
