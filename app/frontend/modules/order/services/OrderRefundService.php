<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/3/2
 * Time: 下午5:01
 */

namespace app\frontend\modules\services\order;


class OrderRefundService
{
    public function orderRefund($order, $order_refund)
    {
        if (empty($order['refundstate'])) {
            message('订单未申请退款，不需处理！');
        }
    }
}