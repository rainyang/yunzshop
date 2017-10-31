<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/27
 * Time: 下午2:20
 */

namespace app\frontend\modules\payment;

use Illuminate\Support\Collection;

/**
 * 所有已开启的订单支付设置
 * Class OrderPaymentSettingCollection
 * @package app\frontend\modules\payment
 */
class OrderPaymentSettingCollection extends Collection
{
    /**
     * 是否开启
     * @return bool
     */
    public function isEnable()
    {
        $settings = $this->sortByDesc(function (OrderPaymentSettingInterface $setting) {
            return $setting->getWeight();
        });

        /**
         * 以影响范围排序,从大到小
         */
        $canNotPay = $settings->contains(function (OrderPaymentSettingInterface $orderPaymentSetting) {
            return !$orderPaymentSetting->canPay();
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
     * todo 满足支付使用条件
     * @return bool
     */
    public function canPay(){
        return true;
    }
    /**
     * todo 过滤无效的
     */
}