<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/3/1
 * Time: 下午4:57
 */

namespace app\frontend\modules\order\services;

use app\frontend\modules\order\services\model\behavior;

class OrderRefundService
{
    public $raid;
    public $message;
    public $order_id;

    function __construct()
    {
        $this->raid = \YunShop::request()->raid;
        $this->message = \YunShop::request()->message;
    }

    public function orderRefund($order)
    {
        $this->order_id = $order['id'];
        if (empty($order['refundstate'])) {
            message('订单未申请退款，不需处理');
        }
        $order_refund = behavior\OrderRefund::getDbRefund($order['refundid']);
        if (empty($order_refund)) {
            $order_data = [
                'refundstate' => 0
            ];
            behavior\Order::updateOrder($order['id'], $order_data);
        }
        if (empty($order_refund['refundno'])) {
            $order_refund['refundno'] = m('common')->createNO('order_refund', 'refundno', 'SR');
            $data['refundno'] = $order_refund['refundno'];
            behavior\OrderRefund::updateRefund($order_refund, $data);
        }
        $refundstatus = intval(\YunShop::request()->refundstatus);
        $refundcontent = trim(\YunShop::request()->refundcontent);
        switch ($refundstatus)
        {
            case '0':
                $this->waitingHandle();
                break;
            case '3':
                $this->refundAndReturnGoods($order_refund);
                break;
            case '-1':
                $this->rejectApply($refundcontent, $order['refundid']);
                break;
            case '1':
                $this->passRefund($order);
                break;
            case '2':
                $this->manualRefund();
                break;
        }
    }

    //暂不处理
    private function waitingHandle()
    {
        message('暂不处理', referer());
    }

    //同意退款，并要求客户把商品寄回
    private function refundAndReturnGoods($order_refund)
    {
        $data = array();
        $data['reply'] = '';
        $data['refundaddressid'] = $this->raid;
        $data['message'] = $this->message;
        if (empty($order_refund['operatetime'])) {
            $data['operatetime'] = time();
        }
        if ($order_refund['status'] != 4) {
            $data['status'] = 3;
        }
        behavior\OrderRefund::updateRefund($order_refund, $data);
    }

    //驳回申请
    private function rejectApply($refundcontent, $order_refund)
    {
        $data = array();
        $data['reply'] = $refundcontent;
        $data['status'] = -1;
        behavior\OrderRefund::updateRefund($order_refund, $data);
    }

    //手动退款
    private function manualRefund($order_refund)
    {
        $data['reply'] = '';
        $data['status'] = 1;
        $data['refundtype'] = 2;
        $data['price'] = $order_refund['applyprice'];
        $data['refundtime'] = time();
        behavior\OrderRefund::updateRefund($order_refund, $data);
        $order_data = [
            'refundstate' => 0,
            'status' => -1,
            'refundtime' => time()
        ];
        behavior\Order::updateOrder($this->order_id, $order_data);
        //通过订单id去查order_goods表和goods表  取出g.id,g.credit, o.total,o.realprice，给商品加库存
    }

    //同意退款
    private function passRefund($order, $order_refund)
    {
        //退款需要支付接口，扣除积分，扣除余额
        $data = array();
        $data['reply'] = '';
        $data['status'] = 1;
        $data['refundtype'] = 0;
        $data['price'] = $order_refund['applyprice'];
        $data['refundtime'] = time();
        behavior\OrderRefund::updateRefund($order_refund, $data);
        //通过订单id去查order_goods表和goods表  取出g.id,g.credit, o.total,o.realprice，给商品加库存
    }
}