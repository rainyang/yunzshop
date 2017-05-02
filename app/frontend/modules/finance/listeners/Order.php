<?php

namespace app\frontend\modules\finance\listeners;

use app\common\events\discount\OnDiscountInfoDisplayEvent;

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/5/2
 * Time: 上午10:59
 */
class Order
{
    public function onDisplay(OnDiscountInfoDisplayEvent $event)
    {
        $this->event = $event;
        $orderModel = $this->event->getOrderModel();

        $data = [];

        $event->addMap('deduction', $data);

    }

    public function subscribe($events)
    {
        $events->listen(
            OnDiscountInfoDisplayEvent::class,
            self::class . '@onDisplay'
        );
//        $events->listen(
//            \app\common\events\order\AfterOrderCreatedEvent::class,
//            Express::class . '@onSave'
//        );
    }
}