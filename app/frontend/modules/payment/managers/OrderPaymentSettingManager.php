<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/27
 * Time: 下午5:20
 */

namespace app\frontend\modules\payment\managers;

use app\common\models\Order;
use app\frontend\modules\payment\OrderPaymentSettings\OrderPaymentSettingCollection;
use app\frontend\modules\payment\OrderPaymentSettings\OrderPaymentSettingInterface;
use Illuminate\Container\Container;

/**
 * 订单设置管理者
 * Class OrderPaymentSettingManager
 * @package app\frontend\modules\payment\managers
 */
class OrderPaymentSettingManager extends Container
{

    /**
     * 获取订单支付方式的设置集合
     * @param Order $order
     * @return OrderPaymentSettingCollection
     */
    public function getOrderPaymentSettingCollection(Order $order)
    {
        $settings = collect($this->getBindings())->map(function ($value, $key) use ($order) {
            return $this->make($key, $order);
        })->filter(function (OrderPaymentSettingInterface $setting) {
            // 可用的
            return $setting->exist();
        });
        return new OrderPaymentSettingCollection($settings);
    }
}