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
    public function refund($order, $to)
    {
        $refund_class = ('services\\' . $this->to . 'Service');
        $refund_class::refund($order);
    }
}