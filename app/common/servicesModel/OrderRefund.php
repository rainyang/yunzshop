<?php

/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/3/1
 * Time: 上午9:58
 */
namespace app\common\servicesModel;


class OrderRefund
{
    public static function getOrderRefund($refund_model)
    {
        \app\common\models\OrderRefund::where('id', '=', $refund_model['id'])
            ->where('status', '=', '0')
            ->orWhere('status', '>', '1')
            ->limit(1);
    }

    public static function updateOrderRefund($refund_model)
    {
        \app\common\models\OrderRefund::update($refund_model['data'])
            ->where('id', '=', $refund_model['id']);
    }
}