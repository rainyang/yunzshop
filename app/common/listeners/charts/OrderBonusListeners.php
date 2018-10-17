<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/10/6
 * Time: 11:18
 */
namespace app\common\listeners\charts;

use app\common\events\order\AfterOrderReceivedEvent;
use app\common\events\order\AfterOrderPaidEvent;
use app\Jobs\OrderBonusContentJob;
use app\Jobs\OrderBonusStatusJob;
use app\Jobs\OrderBonusUpdateJob;
use Illuminate\Foundation\Bus\DispatchesJobs;
use app\common\models\Order;

class OrderBonusListeners
{
    use DispatchesJobs;
    protected $orderModel;

    public function subscribe($events)
    {
        //支付之后 统计订单详情
        $events->listen(
            AfterOrderPaidEvent::class,
            OrderBonusListeners::class . '@addBonus'
        );

        //收货之后 更改订单状态
        $events->listen(
            AfterOrderReceivedEvent::class,
            OrderBonusListeners::class . '@updateBonus'
        );



        //订单关闭 分红插入回滚
//        $events->listen(
//            AfterOrderCanceledEvent::class,
//            OrderBonusListeners::class . '@orderCancel'
//        );
    }

    public function addBonus(AfterOrderPaidEvent $event)
    {
        $this->orderModel = Order::find($event->getOrderModel()->id);
        $this->dispatch(new OrderBonusContentJob($this->orderModel));
    }


    public function updateBonus(AfterOrderReceivedEvent $event)
    {
        $this->dispatch(new OrderBonusStatusJob($event->getOrderModel()->id));
    }
}