<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/11/7
 * Time: 下午5:59
 */

namespace app\frontend\modules\payment;

use app\common\models\Order;
use app\frontend\modules\payment\managers\OrderPaymentTypeSettingManager;
use app\frontend\modules\payment\orderPayments\AppPayment;
use app\frontend\modules\payment\orderPayments\CloudPayment;
use app\frontend\modules\payment\orderPayments\WebPayment;
use app\frontend\modules\payment\paymentSettings\shop\AlipayAppSetting;
use app\frontend\modules\payment\paymentSettings\shop\AlipaySetting;
use app\frontend\modules\payment\paymentSettings\shop\BalanceSetting;
use app\frontend\modules\payment\paymentSettings\shop\CloudPayWechatSetting;
use app\frontend\modules\payment\paymentSettings\shop\WechatAppPaySetting;
use app\frontend\modules\payment\paymentSettings\shop\WechatPaySetting;

class PaymentConfig
{
    static function get(){
        return [
            'balance' => [
                'settings' => [
                    'shop' => function (OrderPaymentTypeSettingManager $manager, Order $order) {
                        return new BalanceSetting($order);
                    }
                ],
            ],
            'alipay' => [
                'payment' => function ($payType, $settings) {
                    return new WebPayment($payType, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPaymentTypeSettingManager $manager, Order $order) {
                        return new AlipaySetting($order);
                    }
                ],
            ]
            , 'wechatPay' => [
            'payment' => function ($payType, $settings) {
                return new WebPayment($payType, $settings);
            },
            'settings' => [
                'shop' => function (OrderPaymentTypeSettingManager $manager, Order $order) {
                    return new WechatPaySetting($order);
                }
            ],
        ], 'alipayApp' => [
            'payment' => function ($payType, $settings) {
                return new AppPayment($payType, $settings);
            },
            'settings' => [
                'shop' => function (OrderPaymentTypeSettingManager $manager, Order $order) {
                    return new AlipayAppSetting($order);
                }
            ],
        ], 'cloudPayWechat' => [
            'payment' => function ($payType, $settings) {
                return new CloudPayment($payType, $settings);
            },
            'settings' => [
                'shop' => function (OrderPaymentTypeSettingManager $manager, Order $order) {
                    return new CloudPayWechatSetting($order);
                }
            ],
        ], 'wechatAppPay' => [
            'payment' => function ($payType, $settings) {
                    return new AppPayment($payType, $settings);
            },
            'settings' => [
                    'shop' => function (OrderPaymentTypeSettingManager $manager, Order $order) {
                        return new WechatAppPaySetting($order);
                    }
            ],
        ], 'YunPayWechat' => [
            'payment' => function ($payType, $settings) {
                    return new CloudPayment($payType, $settings);
            },
            'settings' => [
                    'shop' => function (OrderPaymentTypeSettingManager $manager, Order $order) {
                        return new CloudPayWechatSetting($order);
                    }
            ],
        ],
        ];
    }
}