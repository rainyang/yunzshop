<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/7
 * Time: 下午4:07
 */

namespace app\frontend\modules\payment\listeners;

use app\common\events\payment\GetOrderPaymentTypeEvent;
use app\common\events\payment\RechargeComplatedEvent;

class EupPay
{
    public function onGetPaymentTypes(GetOrderPaymentTypeEvent $event)
    {
        $set = \Setting::get('plugin.eup_pay');

        if (\YunShop::plugin()->get('eup-pay') && !is_null($set)) {

            $result = [
                'name' => 'EUP支付',
                'value' => '16',
                'need_password' => '0'

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
        $events->listen(
            RechargeComplatedEvent::class,
            self::class . '@onGetPaymentTypes'
        );
    }
}