<?php
namespace app\frontend\modules\payment\listeners;

use app\common\events\payment\GetOrderPaymentTypeEvent;
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/5/17
 * Time: 下午5:44
 */
class Credit
{
    public function onGetPaymentTypes(GetOrderPaymentTypeEvent $event)
    {
        if (\Setting::get('shop.pay.credit')) {
            $result = [
                'name' => '余额支付',
                'value' => '3'
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