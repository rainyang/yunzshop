<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/8/2
 * Time: 下午5:51
 */

namespace app\frontend\modules\order\operations\member;


use app\frontend\modules\order\operations\OrderOperation;

class Refunded extends OrderOperation
{
    public function getValue()
    {
        return static::REFUND_INFO;
    }
    public function getName()
    {
        return '已退款';
    }
    public function enable()
    {
        return $this->order->isRefunded();

    }
}