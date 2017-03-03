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
    public $order_refund;

    function __construct()
    {
        $this->raid = \YunShop::request()->raid;
        $this->message = \YunShop::request()->message;
    }

    public function refund($order)
    {
        if (empty($order['refundstate'])) {
            message('订单未申请退款，不需处理');
        }
        $this->order = $order;
        $this->order_refund = OrderRefund::find($order->refundid);
        if (!$this->order_refund) {
            $this->order->refundstate = 0;
            $this->order->save();
        }
        if ($this->order_refund->refundno) {
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
                $this->refundAndReturnGoods();
                break;
            case '-1':
                $this->rejectApply($refundcontent);
                break;
            case '1':
                $this->passRefund();
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
    private function refundAndReturnGoods()
    {
        $this->order_refund->reply = '';
        $this->order_refund->refundaddressid = $this->raid;
        $this->order_refund->message = $this->message;
        if (!$this->order_refund->operatetime) {
            $this->order_refund->operatetime = time();
        }
        if ($this->order_refund['status'] != 4) {
            $this->order_refund->status = 3;
        }
        $this->order_refund->save();
    }

    //驳回申请
    private function rejectApply($refundcontent)
    {
        $this->order_refund->reply = $refundcontent;
        $this->order_refund->status = -1;
        $this->order_refund->save();
    }

    //手动退款
    private function manualRefund()
    {
        $this->order_refund->reply = '';
        $this->order_refund->status = 1;
        $this->order_refund->refundtype = 2;
        $this->order_refund->price = $this->order_refund->applyprice;
        $this->order_refund->refundtime = time();
        $this->order_refund->save();

        $this->order->refundstate = 0;
        $this->order->status = -1;
        $this->order->refundtime = time();
        $this->order->save();
        //通过订单id去查order_goods表和goods表  取出g.id,g.credit, o.total,o.realprice，给商品加库存
    }

    //同意退款
    private function passRefund()
    {
        //退款需要支付接口，扣除积分，扣除余额
        $this->order_refund->reply = '';
        $this->order_refund->status = 1;
        $this->order_refund->refundtype = 0;
        $this->order_refund->price = $this->order_refund->applyprice;
        $this->order_refund->refundtime = time();
        $this->order_refund->save();
        //通过订单id去查order_goods表和goods表  取出g.id,g.credit, o.total,o.realprice，给商品加库存
    }
}