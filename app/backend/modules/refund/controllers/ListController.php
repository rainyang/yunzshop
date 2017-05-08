<?php
namespace app\backend\modules\refund\controllers;

/**
 * 退款申请列表
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/13
 * Time: 下午3:04
 */
class ListController extends \app\backend\modules\order\controllers\ListController
{

    public function returnGoods()
    {
        $this->orderModel->whereHas('hasOneRefundApply',function ($query){
            return $query->ReturnGoods();
        });
        return view('order.index', $this->getData())->render();
    }

    public function exchangeGoods()
    {
        $this->orderModel->whereHas('hasOneRefundApply',function ($query){
            return $query->ExchangeGoods();
        });
        return view('order.index', $this->getData())->render();
    }

    /**
     * @return mixed
     * 退换货订单
     */
    public function refundMoney()
    {
        $this->orderModel->whereHas('hasOneRefundApply',function ($query){
            return $query->RefundMoney();
        });
        return view('order.index', $this->getData())->render();
    }

    public function refunded()
    {
        $this->orderModel->refunded();
        return view('order.index', $this->getData())->render();
    }
    public function refund()
    {
        $this->orderModel->refund();
        return view('order.index', $this->getData())->render();
    }
}