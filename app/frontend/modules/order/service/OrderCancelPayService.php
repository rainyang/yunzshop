<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/3/2
 * Time: 上午11:44
 */

namespace app\frontend\modules\order\services;


class OrderCancelPayService
{
    public function orderCancelPay($order)
    {
        if ($order['status'] != 1) {
            message('订单未付款，不需取消！');
        }
        return [
            'status' => 0,
            'cancelpaytime' => time()
        ];
    }
}