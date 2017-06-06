<?php

namespace app\frontend\modules\order\listeners;

use app\common\events\order\AfterOrderCanceledEvent;
use app\common\events\order\AfterOrderCreatedEvent;
use app\common\events\order\AfterOrderPaidEvent;
use app\common\events\order\AfterOrderReceivedEvent;
use app\common\events\order\AfterOrderSentEvent;
use app\common\models\Order;
use app\frontend\modules\order\services\MessageService;
use Illuminate\Contracts\Events\Dispatcher;

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/6/5
 * Time: 下午8:53
 */
class orderListener
{
    public function onCreated(AfterOrderCreatedEvent $event)
    {
        $order = Order::find($event->getOrderModel()->id);
        MessageService::created($order);
    }

    public function onPaid(AfterOrderPaidEvent $event)
    {
        $order = Order::find($event->getOrderModel()->id);
        MessageService::paid($order);
    }

    public function onCanceled(AfterOrderCanceledEvent $event)
    {
        $order = Order::find($event->getOrderModel()->id);
        MessageService::canceled($order);
    }

    public function onSent(AfterOrderSentEvent $event)
    {
        $order = Order::find($event->getOrderModel()->id);
        MessageService::sent($order);
    }

    public function onReceived(AfterOrderReceivedEvent $event)
    {
        $order = Order::find($event->getOrderModel()->id);
        MessageService::received($order);
    }

    public function subscribe(Dispatcher $events)
    {
        $events->listen(AfterOrderCreatedEvent::class, self::class . '@onCreated');
        $events->listen(AfterOrderPaidEvent::class, self::class . '@onPaid');
        $events->listen(AfterOrderCanceledEvent::class, self::class . '@onCanceled');
        $events->listen(AfterOrderSentEvent::class, self::class . '@onSent');
        $events->listen(AfterOrderReceivedEvent::class, self::class . '@onReceived');
    }
}