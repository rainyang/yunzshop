<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/25
 * Time: 下午4:54
 */

namespace app\common\listeners\member\level;


use Illuminate\Events\Dispatcher;
use app\common\facades\Setting;

class LevelListener
{
    /**
     * 会员等级升级 （监听订单）
     */
    public function subscribe(Dispatcher $event)
    {
        $set = Setting::get('shop.member');
        if ($set['level_after'] == 1) {
            $event->listen(
                \app\common\events\order\AfterOrderPaidEvent::class,
                \app\common\services\member\level\LevelUpgradeService::class.'@checkUpgradeAfterPaid'
            );
        } else {
            $event->listen(
                \app\common\events\order\AfterOrderReceivedEvent::class,
                \app\common\services\member\level\LevelUpgradeService::class.'@checkUpgrade'
            );
        }


    }
}
