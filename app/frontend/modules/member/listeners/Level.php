<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/14
 * Time: 下午11:06
 */

namespace app\frontend\modules\member\listeners;


use app\backend\modules\member\models\MemberLevel;
use app\frontend\modules\member\services\MemberLevelService;

class Level
{
    public function onReceived($event){
        $order_model = $event->getOrderModel();

//dd(4312);
        $result = (new MemberLevelService())->test($order_model);
        if ($result === true) {
            return true;
        }
        // todo 增加失败日志 $result
    }
    public function subscribe($events)
    {
        $events->listen(
            \app\common\events\order\AfterOrderReceivedEvent::class,
            \app\frontend\modules\member\listeners\Level::class.'@onReceived'
        );

    }
}