<?php

/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/3/2
 * Time: 下午6:08
 */

namespace app\frontend\modules\order\services\behavior;

class OrderRefundService
{
    //传order对象
    public static function refund($order, $to)
    {
        $class_name = 'app\frontend\modules\order\services\behavior\\' . ucwords($to) . 'OrderService';
        $refund_class = new $class_name();
        $refund_class->refund($order);
    }
}