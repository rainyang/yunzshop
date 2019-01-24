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
    public function handle(AfterOrderCreatedEvent $event)
    {
        $orderModel = $event->getOrderModel();
        $order = Order::find($orderModel->id);
        $order->no_refund = $this->isNotRefund($order);
        $order->save();
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