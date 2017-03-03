<?php

/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/3/2
 * Time: 下午6:08
 */

namespace app\frontend\modules\order\services\behavior;

class OrderRefund
{
    //传order对象
    public function refund($order, $to)
    {
        $refund_class = (ucwords($this->to) . 'OrderService');
        $refund_class::refund($order);
    }
}