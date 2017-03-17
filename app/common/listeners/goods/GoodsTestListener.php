<?php
namespace app\common\listeners\goods;
use app\common\events\Event;
use app\common\listeners\Opinion;

/**
 * Created by PhpStorm.s
 * User: shenyang
 * Date: 2017/3/17
 * Time: 上午11:38
 */
class GoodsTestListener extends \app\common\listeners\EventListener
{
    public function onTest(Event $event){
       //$event->setOpinion(new Opinion(false,'库存不足'));
    }
    public function subscribe($events)
    {
        $events->listen(
            \app\common\events\order\BeforeOrderGoodsAddInOrder::class,
            \app\common\listeners\goods\GoodsTestListener::class . '@onTest'
        );
    }
}