<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/27
 * Time: 下午2:20
 */

namespace app\frontend\modules\payment\orderPaymentSettings;

use Illuminate\Support\Collection;

/**
 * 所有已开启的订单支付设置
 * Class OrderPaymentSettingCollection
 * @package app\frontend\modules\payment
 */
class OrderPaymentSettingCollection extends Collection
{
    /**
     * 是否可用
     * @return bool
     */
    public function canUse()
    {
        if ($this->isEmpty()) {
            return false;
        }
        $settings = $this->sortByDesc(function (OrderPaymentSettingInterface $setting) {
            return $setting->getWeight();
        });

        /**
         * 以影响范围排序,从大到小
         */

        $canNotPay = $settings->contains(function (OrderPaymentSettingInterface $orderPaymentSetting) {

            return !$orderPaymentSetting->canUse();
        });

        return !$canNotPay;
    }

    /**
     * 排序序列
     * @return int
     */
    public function index()
    {
        return 1;
    }

    /**
     * todo 过滤无效的
     */
}