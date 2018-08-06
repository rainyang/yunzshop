<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/8/2
 * Time: 下午5:51
 */

namespace app\frontend\modules\order\operations\member;


use app\frontend\modules\order\operations\OrderOperation;

class Refunding extends OrderOperation
{
    public function getValue()
    {
        return static::REFUND_INFO;
    }
    public function getName()
    {
        return '退款中';
    }
    public function enable()
    {
        return $this->order->isRefunding();

    }
}