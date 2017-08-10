<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/8/5
 * Time: 上午4:15
 */

namespace app\frontend\modules\payment\listeners;


use app\common\events\payment\GetOrderPaymentTypeEvent;

class CloudPay
{
    public function onGetPaymentTypes(GetOrderPaymentTypeEvent $event)
    {
        $set = \Setting::get('plugin.cloud_pay_set');
        if (\YunShop::plugin()->get('cloud-pay') && !is_null($set) && 1 == $set['switch']) {
            $result = [
                'name' => '云收银支付',
                'value' => '6'
            ];
            $event->addData($result);

        }
        return null;
    }

    public function subscribe($events)
    {
        $events->listen(
            GetOrderPaymentTypeEvent::class,
            self::class . '@onGetPaymentTypes'
        );
    }
}