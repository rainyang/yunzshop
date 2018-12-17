<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/8/2
 * Time: 下午5:51
 */

namespace app\frontend\modules\order\operations\member;


use app\frontend\modules\order\operations\OrderOperation;

class ApplyRefund extends OrderOperation
{
    public function getApi()
    {
        if ($this->no_refund) {
            return \Setting::get('shop.shop')['cservice'];
        }
        return 'refund.apply.store';
    }
    public function getValue()
    {
        return static::REFUND;
    }
    public function getName()
    {
        return '申请退款';
    }
    public function enable()
    {
        //2018-8-30 租赁订单不能退款
        if ($this->order->plugin_id == 40) {
            return false;
        }
        //商品开启不可退款
        if ($this->no_refund) {
            return false;
        }
        return $this->order->canRefund();
    }

}