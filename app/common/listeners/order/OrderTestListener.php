<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 23/02/2017
 * Time: 21:48
 */

namespace app\common\listeners\order;


use app\common\events\order\BeforeOrderStatusChangeEvent;
use app\common\listeners\Opinion;

class OrderTestListener
{

    public function __construct()
    {

    }

    public function onTest(BeforeOrderStatusChangeEvent $event)
    {
        $event->setOpinion(new Opinion(false,'插件反对执行'));

        return false;
    }

    public function onTest2(BeforeOrderStatusChangeEvent $even)
    {

        $even->addFeedback(
            [
                'passed' => false,
                'reason' => '未发货',
            ]
        );
        dd($even);
        return false;
    }

    public function subscribe($events)
    {
        $events->listen(
            \app\common\events\order\BeforeOrderCancelPayEvent::class,
            \app\common\listeners\order\OrderTestListener::class . '@onTest'
        );
        return;
        $events->listen(
            \app\common\events\order\AfterOrderCanceledEvent::class,
            \app\common\listeners\order\OrderTestListener::class . '@onTest'
        );
        $events->listen(
            \app\common\events\order\AfterOrderCancelPaidEvent::class,
            \app\common\listeners\order\OrderTestListener::class . '@onTest'
        );
        $events->listen(
            \app\common\events\order\AfterOrderSentEvent::class,
            \app\common\listeners\order\OrderTestListener::class . '@onTest'
        );
        $events->listen(
            \app\common\events\order\AfterOrderPaidEvent::class,
            \app\common\listeners\order\OrderTestListener::class . '@onTest'
        );
        $events->listen(
            \app\common\events\order\AfterOrderReceivedEvent::class,
            \app\common\listeners\order\OrderTestListener::class . '@onTest'
        );
        $events->listen(
            \app\common\events\order\AfterOrderSentEvent::class,
            \app\common\listeners\order\OrderTestListener::class . '@onTest'
        );
    }

}