<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/11/7
 * Time: 下午5:59
 */

namespace app\frontend\modules\payment;

use app\common\models\PayType;
use app\frontend\models\OrderPay;
use app\frontend\modules\payment\managers\OrderPaymentTypeSettingManager;
use app\frontend\modules\payment\orderPayments\AnotherPayment;
use app\frontend\modules\payment\orderPayments\AppPayment;
use app\frontend\modules\payment\orderPayments\CloudAliPayment;
use app\frontend\modules\payment\orderPayments\CloudPayment;

use app\frontend\modules\payment\orderPayments\HuanxunPayment;
use app\frontend\modules\payment\orderPayments\CODPayment;
use app\frontend\modules\payment\orderPayments\CreditPayment;
use app\frontend\modules\payment\orderPayments\RemittancePayment;
use app\frontend\modules\payment\orderPayments\WebPayment;
use app\frontend\modules\payment\orderPayments\YunAliPayment;
use app\frontend\modules\payment\orderPayments\YunPayment;
use app\frontend\modules\payment\paymentSettings\OrderPaymentSettingCollection;
use app\frontend\modules\payment\paymentSettings\shop\AlipayAppSetting;
use app\frontend\modules\payment\paymentSettings\shop\AlipaySetting;
use app\frontend\modules\payment\paymentSettings\shop\AnotherPaySetting;
use app\frontend\modules\payment\paymentSettings\shop\BalanceSetting;
use app\frontend\modules\payment\paymentSettings\shop\CloudPayAliSetting;
use app\frontend\modules\payment\paymentSettings\shop\CloudPayWechatSetting;
use app\frontend\modules\payment\paymentSettings\shop\HuanxunPaySetting;
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
                'payment' => function (OrderPay $orderPay, PayType $payType, OrderPaymentSettingCollection $settings) {
                    return new CreditPayment($orderPay, $payType, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPaymentTypeSettingManager $manager, OrderPay $orderPay) {
                        return new BalanceSetting($orderPay);
                    }
                ],
            ],
            'alipay' => [
                'payment' => function (OrderPay $orderPay, PayType $payType, OrderPaymentSettingCollection $settings) {
                    return new WebPayment($orderPay, $payType, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPaymentTypeSettingManager $manager, OrderPay $orderPay) {
                        return new AlipaySetting($orderPay);
                    }
                ],
            ]
            , 'wechatPay' => [
                'payment' => function (OrderPay $orderPay, PayType $payType, OrderPaymentSettingCollection $settings) {
                    return new WebPayment($orderPay, $payType, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPaymentTypeSettingManager $manager, OrderPay $orderPay) {
                        return new WechatPaySetting();
                    }
                ],
            ], 'alipayApp' => [
                'payment' => function (OrderPay $orderPay, PayType $payType, OrderPaymentSettingCollection $settings) {
                    return new AppPayment($orderPay, $payType, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPaymentTypeSettingManager $manager, OrderPay $orderPay) {
                        return new AlipayAppSetting($orderPay);
                    }
                ],
            ], 'cloudPayWechat' => [
                'payment' => function (OrderPay $orderPay, PayType $payType, OrderPaymentSettingCollection $settings) {
                    return new CloudPayment($orderPay, $payType, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPaymentTypeSettingManager $manager, OrderPay $orderPay) {
                        return new CloudPayWechatSetting($orderPay);
                    }
                ],
            ], 'wechatApp' => [
                'payment' => function (OrderPay $orderPay, PayType $payType, OrderPaymentSettingCollection $settings) {

                    return new AppPayment($orderPay, $payType, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPaymentTypeSettingManager $manager, OrderPay $orderPay) {
                        return new WechatAppPaySetting();
                    }
                ],
            ], 'yunPayWechat' => [
                'payment' => function (OrderPay $orderPay, PayType $payType, OrderPaymentSettingCollection $settings) {
                    return new YunPayment($orderPay, $payType, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPaymentTypeSettingManager $manager, OrderPay $orderPay) {
                        return new YunPayWechatSetting($orderPay);
                    }
                ],
            ], 'cloudPayAlipay' => [
                'payment' => function (OrderPay $orderPay, PayType $payType, OrderPaymentSettingCollection $settings) {
                    return new CloudAliPayment($orderPay, $payType, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPaymentTypeSettingManager $manager, OrderPay $orderPay) {
                        return new CloudPayAliSetting($orderPay);
                    }
                ],
            ], 'anotherPay' => [
                'payment' => function (OrderPay $orderPay, PayType $payType, OrderPaymentSettingCollection $settings) {
                    return new AnotherPayment($orderPay, $payType, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPaymentTypeSettingManager $manager, OrderPay $orderPay) {
                        return new AnotherPaySetting($orderPay);
                    }
                ],
            ], 'yunPayAlipay' => [
                'payment' => function (OrderPay $orderPay, PayType $payType, OrderPaymentSettingCollection $settings) {
                    return new YunAliPayment($orderPay, $payType, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPaymentTypeSettingManager $manager, OrderPay $orderPay) {
                        return new YunPayAliSetting($orderPay);
                    }
                ],
            ], 'huanxunQuick' => [
                'payment' => function (OrderPay $orderPay, PayType $payType, OrderPaymentSettingCollection $settings) {
                    return new HuanxunPayment($orderPay, $payType, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPaymentTypeSettingManager $manager, OrderPay $orderPay) {
                        return new HuanxunPaySetting($orderPay);
                    }
                ],
            ],
            'COD' => [
                'payment' => function (OrderPay $orderPay, PayType $payType, OrderPaymentSettingCollection $settings) {
                    return new CODPayment($orderPay, $payType, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPaymentTypeSettingManager $manager, OrderPay $orderPay) {
                        return new CODSetting($orderPay);
                    }
                ],
            ],
            'remittance' => [
                'payment' => function (OrderPay $orderPay, PayType $payType, OrderPaymentSettingCollection $settings) {
                    return new RemittancePayment($orderPay, $payType, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPaymentTypeSettingManager $manager, OrderPay $orderPay) {
                        return new RemittanceSetting($orderPay);
                    }
                ],
            ],
        ];
    }
}