<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/25
 * Time: 下午4:54
 */

namespace app\common\listeners\member\level;


class LevelListener
{
    /**
     * 会员等级升级 （监听订单）
     * @param AfterOrderReceivedEvent $event
     */
    public function subscribe(AfterOrderReceivedEvent $event)
    {
        $event->listen(
            \app\common\events\order\AfterOrderReceivedEvent::class,
            \app\common\services\member\level\LevelService::class.'@checkUpgrade'
        );

    }
}
