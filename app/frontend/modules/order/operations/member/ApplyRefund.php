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
        return $this->order->canRefund();
    }
}