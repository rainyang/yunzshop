<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/27
 * Time: 上午9:56
 */

namespace app\frontend\modules\payment\managers;

use app\common\models\Order;
use app\common\models\PayType;
use app\frontend\modules\payment\orderPayments\AppPayment;
use app\frontend\modules\payment\orderPayments\CloudPayment;
use app\frontend\modules\payment\orderPayments\NormalPayment;
use app\frontend\modules\payment\orderPayments\WebPayment;
use app\frontend\modules\payment\orderPaymentSettings\shop\AlipayAppSetting;
use app\frontend\modules\payment\orderPaymentSettings\shop\AlipaySetting;
use app\frontend\modules\payment\orderPaymentSettings\shop\BalanceSetting;
use app\frontend\modules\payment\orderPaymentSettings\shop\CloudPayWechatSetting;
use app\frontend\modules\payment\orderPaymentSettings\shop\WechatAppPaySetting;
use app\frontend\modules\payment\orderPaymentSettings\shop\WechatPaySetting;
use Illuminate\Container\Container;
use Illuminate\Support\Collection;

/**
 * 订单支付管理者
 * Class OrderPaymentManager
 * @package app\frontend\modules\payment\managers
 */
class OrderPaymentManager extends Container
{
    /**
     * @var PaymentManager
     */
    private $paymentManager;
    /**
     * @var array
     */
    private $paymentConfig;

    /**
     * OrderPaymentManager constructor.
     * @param PaymentManager $paymentManager
     */
    function __construct(PaymentManager $paymentManager)
    {
        $this->paymentManager = $paymentManager;

        // 支付设置数组 todo 使用laravel的config定义并读取
        $this->paymentConfig = [
            'balance' => [
                'settings' => [
                    'shop' => function (OrderPaymentSettingManager $manager, Order $order) {
                        return new BalanceSetting($order);
                    }
                ],
            ],
            'alipay' => [
                'payment' => function ($code, $order, $settings) {
                    return new WebPayment($code, $order, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPaymentSettingManager $manager, Order $order) {
                        return new AlipaySetting($order);
                    }

                ],
            ]
            , 'wechatPay' => [
                'payment' => function ($code, $order, $settings) {
                    return new WebPayment($code, $order, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPaymentSettingManager $manager, Order $order) {
                        return new WechatPaySetting($order);
                    }

                ],
            ], 'alipayApp' => [
                'payment' => function ($code, $order, $settings) {
                    return new AppPayment($code, $order, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPaymentSettingManager $manager, Order $order) {
                        return new AlipayAppSetting($order);
                    }

                ],
            ], 'cloudPayWechat' => [
                'payment' => function ($code, $order, $settings) {
                    return new CloudPayment($code, $order, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPaymentSettingManager $manager, Order $order) {
                        return new CloudPayWechatSetting($order);
                    }

                ],

            ],
            'wechatAppPay' => [
                'payment' => function ($code, $order, $settings) {
                    return new AppPayment($code, $order, $settings);
                },
                'settings' => [
                    'shop' => function (OrderPaymentSettingManager $manager, Order $order) {
                        return new WechatAppPaySetting($order);
                    }

                ],
            ]
        ];
    }

    public function addPaymentConfig($paymentConfig)
    {
        $this->paymentConfig = array_merge_recursive($this->paymentConfig, $paymentConfig);
    }

    private function bindPaymentAndSettings()
    {
        // 支付方式集合
        collect($this->paymentConfig)->each(function ($payment, $code) {
            /**
             * 分别绑定支付方式与支付方式设置类. 只定义不实例化,以便于插件在支付方式实例化之前,追加支付方式与支付方式的设置类
             */
            // 绑定支付方式
            $this->bind($code, function (OrderPaymentManager $manager, Order $order) use ($code, $payment) {
                /**
                 * @var OrderPaymentSettingManager $settingManager
                 */
                $settingManager = app('PaymentManager')->make('OrderPaymentSettingManagers')->make($code);
                $settings = $settingManager->getOrderPaymentSettingCollection($order);

                if (isset($payment['payment']) && $payment['payment'] instanceof \Closure) {
                    return call_user_func($payment['payment'], $code, $order, $settings);
                }
                return new NormalPayment($code, $order, $settings);
            });


            // 绑定支付方式对应的设置
            app('PaymentManager')->make('OrderPaymentSettingManagers')->singleton($code, function (OrderPaymentSettingManagers $managers) use ($payment) {
                // 支付方式
                $manager = new OrderPaymentSettingManager();

                // 对应设置集合
                foreach ($payment['settings'] as $key => $setting) {
                    $manager->singleton($key, $setting);
                }
                return $manager;
            });

        });
    }

    /**
     * 获取订单可用的支付方式
     * @param $order
     * @return Collection
     */
    public function getOrderPaymentTypes($order)
    {
        $this->bindPaymentAndSettings();

        /**
         * 商城中存在的支付方式集合
         * @var \Illuminate\Database\Eloquent\Collection $paymentTypes
         */
        $paymentTypes = PayType::get();
        if ($paymentTypes->isEmpty()) {
            return collect();
        }

        // 实例化订单所有支付方式
        $orderPaymentTypes = $paymentTypes->map(function (PayType $payType) use ($order) {

            // 对应的类在容器中注册过
            if ($this->bound($payType->code)) {
                return $this->make($payType->code, $order);
            }
            return null;
        });

        // 过滤掉无效的
        $orderPaymentTypes = $orderPaymentTypes->filter(function (OrderPayment $paymentType) {

            // 可用的
            return isset($paymentType) && $paymentType->canUse();
        });

        return $orderPaymentTypes;
    }
}