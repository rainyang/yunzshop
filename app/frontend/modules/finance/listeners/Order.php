<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/5/2
 * Time: 上午10:59
 */
namespace app\frontend\modules\finance\listeners;

use app\common\events\discount\OnDeductionInfoDisplayEvent;
use app\common\events\discount\OnDeductionPriceCalculatedEvent;

class Order
{
    private $event;
    public function onDisplay(OnDeductionInfoDisplayEvent $event)
    {
        $this->event = $event;
        $orderModel = $this->event->getOrderModel();

        $data = [
            'id'=>'1',//抵扣表id
            'name'=>'积分抵扣',//名称
            'value'=>200,//数值
            'price'=>'20.00',//金额
        ];

        $event->addData($data);

    }
    public function onCalculated(OnDeductionPriceCalculatedEvent $event){
        $this->event = $event;
        $orderModel = $this->event->getOrderModel();

        $data = [
            'id'=>'1',//抵扣表id
            'name'=>'积分抵扣',//名称
            'value'=>200,//数值
            'price'=>'20.00',//金额
        ];

        $event->addData($data);
    }

    public function subscribe($events)
    {
        $events->listen(
            OnDeductionInfoDisplayEvent::class,
            self::class . '@onDisplay'
        );
        $events->listen(

            OnDeductionPriceCalculatedEvent::class,
            self::class . '@onCalculated'
        );

    }
}