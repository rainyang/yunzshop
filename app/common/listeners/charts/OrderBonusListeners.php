<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/10/6
 * Time: 11:18
 */
namespace app\common\listeners\charts;

use app\common\events\order\AfterOrderCanceledEvent;
use app\common\events\order\AfterOrderReceivedEvent;
use app\common\events\order\AfterOrderPaidEvent;
use app\common\events\order\AfterOrderCreatedEvent;
use app\common\events\order\AfterOrderRefundedEvent;
use app\common\models\order\OrderPluginBonus;
use app\Jobs\OrderBonusContentJob;
use app\Jobs\OrderBonusStatusJob;
use app\Jobs\OrderBonusUpdateJob;
use app\Jobs\OrderCountContentJob;
use app\Jobs\OrderCountIncomeJob;
use app\Jobs\OrderCountStatusJob;
use Illuminate\Foundation\Bus\DispatchesJobs;
use app\common\models\Order;

class OrderBonusListeners
{
    use DispatchesJobs;
    protected $orderModel;

    public function subscribe($events)
    {
        //下单
        $events->listen(AfterOrderCreatedEvent::class, OrderBonusListeners::class. '@addCount',999);

        //支付之后 统计订单详情
//        $events->listen(
//            AfterOrderPaidEvent::class,
//            OrderBonusListeners::class . '@addBonus'
//        );

        //收货之后 更改订单状态
        $events->listen(AfterOrderReceivedEvent::class, OrderBonusListeners::class . '@updateBonus');

        //订单取消
        $events->listen(AfterOrderCanceledEvent::class, OrderBonusListeners::class. '@cancel');

        //订单退款
        $events->listen(AfterOrderRefundedEvent::class, OrderBonusListeners::class. '@refunded');

    }

//    public function addBonus(AfterOrderPaidEvent $event)
//    {
//        $this->orderModel = Order::find($event->getOrderModel()->id);
//        $this->dispatch(new OrderBonusContentJob($this->orderModel));
//    }


    public function updateBonus(AfterOrderReceivedEvent $event)
    {
        $this->dispatch(new OrderBonusStatusJob($event->getOrderModel()->id));
        $this->dispatch(new OrderCountIncomeJob($event->getOrderModel()->id));
    }

    public function addCount(AfterOrderCreatedEvent $event)
    {
        $orderModel = Order::find($event->getOrderModel()->id);
        $this->dispatch(new OrderCountContentJob($orderModel));
    }

    public function cancel(AfterOrderCanceledEvent $event)
    {
        $this->dispatch(new OrderCountStatusJob($event->getOrderModel()->id, -1));
    }

    public function refunded(AfterOrderRefundedEvent $event)
    {
        $this->dispatch(new OrderCountStatusJob($event->getOrderModel()->id, -2));
    }

}