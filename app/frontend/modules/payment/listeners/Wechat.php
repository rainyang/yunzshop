<?php
namespace app\frontend\modules\payment\listeners;

use app\common\events\payment\GetOrderPaymentTypeEvent;

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/5/17
 * Time: 下午5:44
 */
class Wechat
{
    public function onGetPaymentTypes(GetOrderPaymentTypeEvent $event)
    {
        if (\Setting::get('shop.pay.weixin')) {
            $result = [
                'name' => '微信支付',
                'value' => '1'
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