<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/27
 * Time: 下午5:20
 */

namespace app\frontend\modules\payment;

use app\common\models\Order;
use Illuminate\Container\Container;

/**
 * 订单设置管理者
 * Class OrderPaymentSettingManager
 * @package app\frontend\modules\payment\managers
 */
abstract class OrderPaymentSettingManager extends Container
{
    public function __construct()
    {
    }

    /**
     * 获取订单支付方式的设置集合
     * @param Order $order
     * @return OrderPaymentSettingCollection
     */
    public function getOrderPaymentSettingCollection(Order $order)
    {
        $settings = collect($this->getBindings())->map(function ($value, $key) use ($order) {
            // 注册过的
            return $this->make($key, $order);
        })->filter(function (OrderPaymentSettingInterface $setting) {
            // 可用的
            return $setting->isEnable();
        });
        return new OrderPaymentSettingCollection($settings);
    }
}