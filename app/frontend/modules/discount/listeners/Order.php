<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/11
 * Time: 上午10:00
 */

namespace app\frontend\modules\discount\listeners;

use app\common\events\discount\OnDeductionInfoDisplayEvent;
use app\common\events\discount\OnDiscountInfoDisplayEvent;

class Order
{
    private $event;

    public function onDisplay(OnDiscountInfoDisplayEvent $event){
        $this->event = $event;

        $orderModel = $event->getOrderModel();
        $deductionData = $this->getDeductionEventData($orderModel);
        $event->addMap('deduction',$deductionData);
    }
    private function getDeductionEventData($order_model){

        $event = new OnDeductionInfoDisplayEvent($order_model);
        event($event);
        return $event->getData();
    }
    public function subscribe($events)
    {
        $events->listen(
            OnDiscountInfoDisplayEvent::class,
            self::class . '@onDisplay'
        );
    }
}