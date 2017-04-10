<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2017/4/8
 * Time: 下午1:59
 */

namespace app\common\listeners\member;

use app\common\events\order\AfterOrderCreatedEvent;

class AfterOrderCreatedListener
{
    public function handle(AfterOrderCreatedEvent $event)
    {
        $model = MemberShopInfo::getMemberShopInfo(\YunShop::app()->getMemberId());
        event(new BecomeAgent(\YunShop::request()->mid(), $model));
    }
}