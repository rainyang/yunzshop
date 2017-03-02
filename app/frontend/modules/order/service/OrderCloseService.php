<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/3/2
 * Time: 下午2:15
 */

namespace app\frontend\modules\order\services;


class OrderCloseService
{
    public function orderClose($order)
    {
        if ($order["status"] == -1) {
            message("订单已关闭，无需重复关闭！");
        } else if ($order["status"] >= 1) {
            message("订单已付款，不能关闭！");
        }
    }

    public function getUpdateOrderData($order)
    {
        return [
            'status'    => -1,
            'refundstate'   => 0,
            'cenceltime'    => time(),
            'remark'        => $order['remark'] . '【商家关闭原因】：' . \YunShop::request()->reson
        ];
    }
}