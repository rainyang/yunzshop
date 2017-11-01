<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/27
 * Time: 下午5:20
 */

namespace app\frontend\modules\payment\managers;

use app\common\models\Order;
use app\frontend\modules\payment\orderPayments\balance\ShopOrderPaymentSetting;
use Illuminate\Container\Container;

/**
 * 订单支付设置管理者
 * Class OrderPaymentSettingManager
 * @package app\frontend\modules\payment\managers
 */
class OrderPaymentSettingManagers extends Container
{
    public function __construct()
    {
        // 支付设置数组
        $payments = [
            'balance' => [
                'settings' => [
                    'shop' => [
                        function (OrderPaymentSettingManager $manager, Order $order) {
                            return new ShopOrderPaymentSetting($order);
                        }
                    ]
                ],
            ]
        ];
        // 支付方式集合
        collect($payments)->each(function ($payment, $code) {
            $this->singleton($code, function (OrderPaymentSettingManagers $managers) use ($payment) {
                // 支付方式
                $manager = new OrderPaymentSettingManager();
                // 对应设置基金和
                foreach ($payment['settings'] as $key => $setting) {
                    $manager->singleton($key, $setting);
                }
                return $manager;
            });
        });
    }
}