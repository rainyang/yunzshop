<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/3/1
 * Time: 下午5:41
 */

namespace app\frontend\modules\order\controllers;

use app\frontend\modules\order\services\model\behavior;
use app\frontend\modules\order\services;

class OrderDetailController
{
    private $order_id;
    private $to;

    function __construct()
    {
        $this->order_id = \YunShop::request()->id;
        $this->to = ucfirst(trim(\YunShop::request()->to));
    }

    public function detail()
    {
        $order = Order::find($this->order_id);
        if (empty($order)) {
            message("抱歉，订单不存在!", referer(), "error");
        }
        $order_refund = OrderRefund::find($order['refund_id']);
        $refund_class = ('service\\Order' . $this->to . 'Service');
        $refund_class::orderRefund($order, $order_refund);
    }

    /*function orderDetail()
    {
        $order = behavior\Order::getDbOrder($this->order_id);
        service\OrderEmpty::isEmpty($order);
        //$shopset = m("common")->getSysset("shop");
        switch ($this->to)
        {
            case 'refund':
                service\OrderRefundService::orderRefund($order);
                break;
            case 'cancepay':
                $data = service\OrderCancelPayService::orderCancelPay($order);
                behavior\Order::updateOrder($order['id'], $data);
                break;
            case 'close':
                service\OrderCloseService::orderClose($order);
                $data = service\OrderCloseService::getUpdateOrderData($order);
                behavior\Order::updateOrder($order['id'], $data);
                break;
        }
    }*/
}