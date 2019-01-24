<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/10/6
 * Time: 11:18
 */

namespace app\common\listeners\order;

use app\common\models\Order;
use app\common\events\order\AfterOrderCreatedEvent;

class OrderNoRefundListener
{
    protected $orderModel;

    public function subscribe($event)
    {
        $event->listen(AfterOrderCreatedEvent::class, OrderNoRefundListener::class. '@noRefund');
    }

    public function noRefund(AfterOrderCreatedEvent $event)
    {
        $orderModel = Order::find($event->getOrderModel()->id);
        $orderModel->no_refund = $this->isNotRefund($orderModel);
        $orderModel->save();
    }

    public function isNotRefund($order)
    {
        if ($order->hasManyOrderGoods) {
            foreach ($order->hasManyOrderGoods as $goods) {
                if ($goods->hasOneGoods->no_refund) {
                    return 1;
                }
            }
        }
        return 0;
    }

}