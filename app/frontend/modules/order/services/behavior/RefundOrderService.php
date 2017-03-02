<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/3/2
 * Time: 下午11:05
 */

namespace app\frontend\modules\order\services\behavior;

use app\common\models\OrderRefund;
use app\common\models\Order;

class RefundOrderService
{
    public $raid;
    public $message;
    public $order;

    function __construct()
    {
        $this->raid = \YunShop::request()->raid;
        $this->message = \YunShop::request()->message;
    }

    public function refund($order)
    {
        $this->order_id = $order['id'];
        if (empty($order['refundstate'])) {
            message('订单未申请退款，不需处理');
        }
        $this->order = Order::find($order['id']);
        $order_refund = OrderRefund::find($order['refundid']);
        if (empty($order_refund)) {
            $this->order->refundstate = 0;
            $this->order->save();
        }
        if (empty($order_refund['refundno'])) {
            $this->order->refundno = m('common')->createNO('order_refund', 'refundno', 'SR');
            $this->order->save();
        }
        $refundstatus = intval(\YunShop::request()->refundstatus);
        $refundcontent = trim(\YunShop::request()->refundcontent);
        switch ($refundstatus)
        {
            case '0':
                $this->waitingHandle();
                break;
            case '3':
                $this->refundAndReturnGoods($order['refundid']);
                break;
            case '-1':
                $this->rejectApply($refundcontent, $order['refundid']);
                break;
            case '1':
                $this->passRefund($order['refundid']);
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
    private function refundAndReturnGoods($order_refund_id)
    {
        $order_refund = OrderRefund::find($order_refund_id);
        $order_refund->reply = '';
        $order_refund->refundaddressid = $this->raid;
        $order_refund->message = $this->message;
        if (empty($order_refund['operatetime'])) {
            $order_refund->operatetime = time();
        }
        if ($order_refund['status'] != 4) {
            $order_refund->status = 3;
        }
        $order_refund->save();
    }

    //驳回申请
    private function rejectApply($refundcontent, $order_refund_id)
    {
        $order_refund = OrderRefund::find($order_refund_id);
        $order_refund->reply = $refundcontent;
        $order_refund->status = -1;
        $order_refund->save();
    }

    //手动退款
    private function manualRefund($order_refund_id)
    {
        $order_refund = OrderRefund::find($order_refund_id);
        $order_refund->reply = '';
        $order_refund->status = 1;
        $order_refund->refundtype = 2;
        $order_refund->price = $order_refund['applyprice'];
        $order_refund->refundtime = time();
        $order_refund->save();

        $this->order->refundstate = 0;
        $this->order->status = -1;
        $this->order->refundtime = time();
        $this->order->save();
        //通过订单id去查order_goods表和goods表  取出g.id,g.credit, o.total,o.realprice，给商品加库存
    }

    //同意退款
    private function passRefund($order_refund_id)
    {
        //退款需要支付接口，扣除积分，扣除余额
        $order_refund = OrderRefund::find($order_refund_id);
        $order_refund->reply = '';
        $order_refund->status = 1;
        $order_refund->refundtype = 0;
        $order_refund->price = $order_refund->applyprice;
        $order_refund->refundtime = time();
        $order_refund->save();
        //通过订单id去查order_goods表和goods表  取出g.id,g.credit, o.total,o.realprice，给商品加库存
    }
}