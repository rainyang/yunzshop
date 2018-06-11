<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/11/7
 * Time: 下午5:59
 */

namespace app\frontend\modules\payment;

use app\common\models\Order;
use app\frontend\models\OrderPay;
use app\frontend\modules\payment\managers\OrderPaymentTypeSettingManager;
use app\frontend\modules\payment\orderPayments\AnotherPayment;
use app\frontend\modules\payment\orderPayments\AppPayment;
use app\frontend\modules\payment\orderPayments\CloudAliPayment;
use app\frontend\modules\payment\orderPayments\CloudPayment;
use app\frontend\modules\payment\orderPayments\WebPayment;
use app\frontend\modules\payment\orderPayments\YunAliPayment;
use app\frontend\modules\payment\orderPayments\YunPayment;
use app\frontend\modules\payment\paymentSettings\shop\AlipayAppSetting;
use app\frontend\modules\payment\paymentSettings\shop\AlipaySetting;
use app\frontend\modules\payment\paymentSettings\shop\AnotherPaySetting;
use app\frontend\modules\payment\paymentSettings\shop\BalanceSetting;
use app\frontend\modules\payment\paymentSettings\shop\CloudPayAliSetting;
use app\frontend\modules\payment\paymentSettings\shop\CloudPayWechatSetting;
use app\frontend\modules\payment\paymentSettings\shop\CODSetting;
use app\frontend\modules\payment\paymentSettings\shop\RemittanceSetting;
use app\frontend\modules\payment\paymentSettings\shop\WechatAppPaySetting;
use app\frontend\modules\payment\paymentSettings\shop\WechatPaySetting;
use app\frontend\modules\payment\paymentSettings\shop\YunPayAliSetting;
use app\frontend\modules\payment\paymentSettings\shop\YunPayWechatSetting;

class PaymentConfig
{
    static function get()
    {
        return [
            'balance' => [
                'settings' => [
                    'shop' => function (OrderPaymentTypeSettingManager $manager, OrderPay $orderPay) {
                        return new BalanceSetting($orderPay);
                    }
                ],
            ],
            'alipay' => [
                'payment' => function ($payType, $settings) {
                    return new WebPayment($payType, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPaymentTypeSettingManager $manager, OrderPay $orderPay) {
                        return new AlipaySetting($orderPay);
                    }
                ],
            ]
            , 'wechatPay' => [
                'payment' => function ($payType, $settings) {
                    return new WebPayment($payType, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPaymentTypeSettingManager $manager, OrderPay $orderPay) {
                        return new WechatPaySetting();
                    }
                ],
            ], 'alipayApp' => [
                'payment' => function ($payType, $settings) {
                    return new AppPayment($payType, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPaymentTypeSettingManager $manager, OrderPay $orderPay) {
                        return new AlipayAppSetting($orderPay);
                    }
                ],
            ], 'cloudPayWechat' => [
                'payment' => function ($payType, $settings) {
                    return new CloudPayment($payType, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPaymentTypeSettingManager $manager, OrderPay $orderPay) {
                        return new CloudPayWechatSetting($orderPay);
                    }
                ],
            ], 'wechatApp' => [
                'payment' => function ($payType, $settings) {

                    return new AppPayment($payType, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPaymentTypeSettingManager $manager, OrderPay $orderPay) {
                        return new WechatAppPaySetting();
                    }
                ],
            ], 'yunPayWechat' => [
                'payment' => function ($payType, $settings) {
                    return new YunPayment($payType, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPaymentTypeSettingManager $manager, OrderPay $orderPay) {
                        return new YunPayWechatSetting($orderPay);
                    }
                ],
            ],'cloudPayAlipay' => [
                'payment' => function ($payType, $settings) {
                    return new CloudAliPayment($payType, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPaymentTypeSettingManager $manager, OrderPay $orderPay) {
                        return new CloudPayAliSetting($orderPay);
                    }
                ],
            ],'anotherPay' => [
                'payment' => function ($payType, $settings) {
                    return new AnotherPayment($payType, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPaymentTypeSettingManager $manager, OrderPay $orderPay) {
                        return new AnotherPaySetting($orderPay);
                    }
                ],
            ], 'yunPayAlipay' => [
                'payment' => function ($payType, $settings) {
                    return new YunAliPayment($payType, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPaymentTypeSettingManager $manager, OrderPay $orderPay) {
                        return new YunPayAliSetting($orderPay);
                    }
                ],
            ],
            'COD' => [
                'settings' => [
                    'shop' => function (OrderPaymentTypeSettingManager $manager, OrderPay $orderPay) {
                        return new CODSetting($orderPay);
                    }
                ],
            ],
            'remittance' => [
                'settings' => [
                    'shop' => function (OrderPaymentTypeSettingManager $manager, OrderPay $orderPay) {
                        return new RemittanceSetting($orderPay);
                    }
                ],
            ],
        ];
    }
}