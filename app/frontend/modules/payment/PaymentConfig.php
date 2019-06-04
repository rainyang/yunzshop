<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/11/7
 * Time: 下午5:59
 */

namespace app\frontend\modules\payment;

use app\common\models\PayType;
use app\common\models\OrderPay;
use app\frontend\modules\payment\managers\OrderPaymentTypeSettingManager;
use app\frontend\modules\payment\orderPayments\AnotherPayment;
use app\frontend\modules\payment\orderPayments\AppPayment;
use app\frontend\modules\payment\orderPayments\CloudAliPayment;
use app\frontend\modules\payment\orderPayments\CloudPayment;

use app\frontend\modules\payment\orderPayments\HuanxunPayment;
use app\frontend\modules\payment\orderPayments\CODPayment;
use app\frontend\modules\payment\orderPayments\CreditPayment;
use app\frontend\modules\payment\orderPayments\RemittancePayment;
use app\frontend\modules\payment\orderPayments\UsdtPayment;
use app\frontend\modules\payment\orderPayments\AlipayPayHjment;
use app\frontend\modules\payment\orderPayments\WechatPayHjment;
use app\frontend\modules\payment\orderPayments\WebPayment;
use app\frontend\modules\payment\orderPayments\WftAlipayPayment;
use app\frontend\modules\payment\orderPayments\YopPayment;
use app\frontend\modules\payment\orderPayments\YunAliPayment;
use app\frontend\modules\payment\orderPayments\YunPayment;
use app\frontend\modules\payment\orderPayments\WftPayment;
use app\frontend\modules\payment\orderPayments\DianBangScanPayment;

use app\frontend\modules\payment\paymentSettings\OrderPaymentSettingCollection;
use app\frontend\modules\payment\paymentSettings\shop\AlipayAppSetting;
use app\frontend\modules\payment\paymentSettings\shop\AlipaySetting;
use app\frontend\modules\payment\paymentSettings\shop\AnotherPaySetting;
use app\frontend\modules\payment\paymentSettings\shop\BalanceSetting;
use app\frontend\modules\payment\paymentSettings\shop\CloudPayAliSetting;
use app\frontend\modules\payment\paymentSettings\shop\CloudPayWechatSetting;
use app\frontend\modules\payment\paymentSettings\shop\HuanxunPaySetting;
use app\frontend\modules\payment\paymentSettings\shop\CODSetting;
use app\frontend\modules\payment\paymentSettings\shop\HuanxunWxPaySetting;
use app\frontend\modules\payment\paymentSettings\shop\RemittanceSetting;
use app\frontend\modules\payment\paymentSettings\shop\UsdtPaySetting;
use app\frontend\modules\payment\paymentSettings\shop\WechatPayHjSetting;
use app\frontend\modules\payment\paymentSettings\shop\AlipayPayHjSetting;
use app\frontend\modules\payment\paymentSettings\shop\WechatAppPaySetting;
use app\frontend\modules\payment\paymentSettings\shop\WechatPaySetting;
use app\frontend\modules\payment\paymentSettings\shop\WftAlipaySetting;
use app\frontend\modules\payment\paymentSettings\shop\YopSetting;
use app\frontend\modules\payment\paymentSettings\shop\YunPayAliSetting;
use app\frontend\modules\payment\paymentSettings\shop\YunPayWechatSetting;
use app\frontend\modules\payment\paymentSettings\shop\WftSetting;
use app\frontend\modules\payment\paymentSettings\shop\DianBangScanSetting;


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
                    'shop' => function (OrderPay $orderPay) {
                        return new BalanceSetting($orderPay);
                    }
                ],
            ],
            'alipay' => [
                'payment' => function (OrderPay $orderPay, PayType $payType, OrderPaymentSettingCollection $settings) {
                    return new WebPayment($orderPay, $payType, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPay $orderPay) {
                        return new AlipaySetting($orderPay);
                    }
                ],
            ]
            , 'wechatPay' => [
                'payment' => function (OrderPay $orderPay, PayType $payType, OrderPaymentSettingCollection $settings) {
                    return new WebPayment($orderPay, $payType, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPay $orderPay) {
                        return new WechatPaySetting($orderPay);
                    }
                ],
            ], 'alipayApp' => [
                'payment' => function (OrderPay $orderPay, PayType $payType, OrderPaymentSettingCollection $settings) {
                    return new AppPayment($orderPay, $payType, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPay $orderPay) {
                        return new AlipayAppSetting($orderPay);
                    }
                ],
            ], 'cloudPayWechat' => [
                'payment' => function (OrderPay $orderPay, PayType $payType, OrderPaymentSettingCollection $settings) {
                    return new CloudPayment($orderPay, $payType, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPay $orderPay) {
                        return new CloudPayWechatSetting($orderPay);
                    }
                ],
            ], 'wechatApp' => [
                'payment' => function (OrderPay $orderPay, PayType $payType, OrderPaymentSettingCollection $settings) {

                    return new AppPayment($orderPay, $payType, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPay $orderPay) {
                        return new WechatAppPaySetting($orderPay);
                    }
                ],
            ], 'yunPayWechat' => [
                'payment' => function (OrderPay $orderPay, PayType $payType, OrderPaymentSettingCollection $settings) {
                    return new YunPayment($orderPay, $payType, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPay $orderPay) {
                        return new YunPayWechatSetting($orderPay);
                    }
                ],
            ], 'cloudPayAlipay' => [
                'payment' => function (OrderPay $orderPay, PayType $payType, OrderPaymentSettingCollection $settings) {
                    return new CloudAliPayment($orderPay, $payType, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPay $orderPay) {
                        return new CloudPayAliSetting($orderPay);
                    }
                ],
            ], 'anotherPay' => [
                'payment' => function (OrderPay $orderPay, PayType $payType, OrderPaymentSettingCollection $settings) {
                    return new AnotherPayment($orderPay, $payType, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPay $orderPay) {
                        return new AnotherPaySetting($orderPay);
                    }
                ],
            ], 'yunPayAlipay' => [
                'payment' => function (OrderPay $orderPay, PayType $payType, OrderPaymentSettingCollection $settings) {
                    return new YunAliPayment($orderPay, $payType, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPay $orderPay) {
                        return new YunPayAliSetting($orderPay);
                    }
                ],
            ], 'huanxunQuick' => [
                'payment' => function (OrderPay $orderPay, PayType $payType, OrderPaymentSettingCollection $settings) {
                    return new HuanxunPayment($orderPay, $payType, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPay $orderPay) {
                        return new HuanxunPaySetting($orderPay);
                    }
                ],
            ],
            'COD' => [
                'payment' => function (OrderPay $orderPay, PayType $payType, OrderPaymentSettingCollection $settings) {
                    return new CODPayment($orderPay, $payType, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPay $orderPay) {
                        return new CODSetting($orderPay);
                    }
                ],
            ],
            'remittance' => [
                'payment' => function (OrderPay $orderPay, PayType $payType, OrderPaymentSettingCollection $settings) {
                    return new RemittancePayment($orderPay, $payType, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPay $orderPay) {
                        return new RemittanceSetting($orderPay);
                    }
                ],
            ],
            'wftPay' => [
                'payment' => function (OrderPay $orderPay, PayType $payType, OrderPaymentSettingCollection $settings) {
                    return new WftPayment($orderPay, $payType, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPay $orderPay) {
                        return new WftSetting($orderPay);
                    }
                ],
            ],
            'wftAlipay' => [
                'payment' => function (OrderPay $orderPay, PayType $payType, OrderPaymentSettingCollection $settings) {
                    return new WftAlipayPayment($orderPay, $payType, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPay $orderPay) {
                        return new WftAlipaySetting($orderPay);
                    }
                ],
            ],
            'DianBangScan' => [
                'payment' => function (OrderPay $orderPay, PayType $payType, OrderPaymentSettingCollection $settings) {
                    return new DianBangScanPayment($orderPay, $payType, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPay $orderPay) {
                        return new DianBangScanSetting($orderPay);
                    }
                ],
            ],
            'yop' => [
                'payment' => function (OrderPay $orderPay, PayType $payType, OrderPaymentSettingCollection $settings) {
                    return new YopPayment($orderPay, $payType, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPay $orderPay) {
                        return new YopSetting($orderPay);
                    }
                ],
            ],
            'UsdtPay' => [
                'payment' => function (OrderPay $orderPay, PayType $payType, OrderPaymentSettingCollection $settings) {
                    return new UsdtPayment($orderPay, $payType, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPay $orderPay) {
                        return new UsdtPaySetting($orderPay);
                    }
                ],
            ],
            'convergePayWechat' => [
                'payment' => function (OrderPay $orderPay, PayType $payType, OrderPaymentSettingCollection $settings) {
                    return new WechatPayHjment($orderPay, $payType, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPay $orderPay) {
                        return new WechatPayHjSetting($orderPay);
                    }
                ],
            ],
            'convergePayAlipay' => [
                'payment' => function (OrderPay $orderPay, PayType $payType, OrderPaymentSettingCollection $settings) {
                    return new AlipayPayHjment($orderPay, $payType, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPay $orderPay) {
                        return new AlipayPayHjSetting($orderPay);
                    }
                ],
            ],
        ];
    }
}