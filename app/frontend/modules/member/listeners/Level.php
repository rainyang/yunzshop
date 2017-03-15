<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/14
 * Time: 下午11:06
 */

namespace app\frontend\modules\member\listeners;


use app\backend\modules\member\models\MemberLevel;

class Level
{
    public function onReceived($event){
        $order_model = $event->getOrderModel();
        dd($order_model);
        MemberLevel::upgradeMemberLevel(0,0,0);

    }
    public function subscribe($events)
    {
        $events->listen(
            \app\common\events\order\AfterOrderReceivedEvent::class,
            \app\frontend\modules\member\listeners\Level::class.'@onReceived'
        );

    }
}