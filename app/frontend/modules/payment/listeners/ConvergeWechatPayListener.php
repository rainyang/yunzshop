<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/6/25
 * Time: 上午 09:47
 */

namespace app\frontend\modules\payment\listeners;

use app\common\events\payment\GetOrderPaymentTypeEvent;
use app\common\events\payment\RechargeComplatedEvent;


class ConvergeWechatPayListener
{
    /**
     * 微信支付-HJ
     *
     * @param GetOrderPaymentTypeEvent $event
     * @return null
     */
    public function onGetPaymentTypes(GetOrderPaymentTypeEvent $event)
    {
        $set = \Setting::get('plugin.convergePay_set');

        if (\YunShop::plugin()->get('converge_pay') && !is_null($set) && 1 == $set['converge_pay_status'] && 1 == $set['wechat']['wechat_status'] && \YunShop::app()->type == 2 ? $set['wechat']['XCX_appid'] : $set['wechat']['GZH_appid'] && \YunShop::request()->type != 7) {
            $result = [
                'name' => '微信支付(HJ)',
                'value' => '28',
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