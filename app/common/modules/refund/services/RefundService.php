<?php

namespace app\common\modules\refund\services;

use app\backend\modules\refund\models\RefundApply;
use app\backend\modules\refund\services\RefundOperationService;
use app\common\events\order\AfterOrderRefundedEvent;
use app\common\exceptions\AdminException;
use app\common\models\finance\Balance;
use app\common\models\PayType;
use app\common\services\credit\ConstService;
use app\common\services\finance\BalanceChange;
use app\common\services\PayFactory;
use app\frontend\modules\finance\services\BalanceService;
use Illuminate\Support\Facades\DB;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/10
 * Time: 下午4:29
 */
class RefundService
{
    protected $refundApply;

    public function pay($request)
    {
        $this->refundApply = RefundApply::find($request->input('refund_id'));
        if (!isset($this->refundApply)) {
            throw new AdminException('未找到退款记录');
        }
        switch ($this->refundApply->order->pay_type_id) {
            case PayType::WECHAT_PAY:
                $result = $this->wechat();
                break;
            case PayType::ALIPAY:
                $result = $this->alipay();
                break;
            case PayType::CREDIT:
                $result = $this->balance();
                break;
            case PayType::BACKEND:
                $result = $this->backend();
                break;
            default:
                $result = false;
                break;
        }
        return $result;
    }

    private function wechat()
    {

        //微信退款 同步改变退款和订单状态
        RefundOperationService::refundComplete(['order_id' => $this->refundApply->order->id]);
        $pay = PayFactory::create($this->refundApply->order->pay_type_id);
        //dd([$this->refundApply->order->hasOneOrderPay->pay_sn, $this->refundApply->order->hasOneOrderPay->amount, $this->refundApply->price]);

        $result = $pay->doRefund($this->refundApply->order->hasOneOrderPay->pay_sn, $this->refundApply->order->hasOneOrderPay->amount, $this->refundApply->price);
        echo 1;
        dd($result);

        if (!$result) {
            throw new AdminException('微信退款失败');
        }
        return $result;
    }

    private function alipay()
    {
        RefundOperationService::refundComplete(['order_id' => $this->refundApply->order->id]);

        $pay = PayFactory::create($this->refundApply->order->pay_type_id);

        $result = $pay->doRefund($this->refundApply->order->hasOneOrderPay->pay_sn, $this->refundApply->order->hasOneOrderPay->amount, $this->refundApply->price);

        if (!$result) {
            throw new AdminException('支付宝退款失败');
        }
        //支付宝退款 等待异步通知后,改变退款和订单的状态
        return $result;
    }

    private function backend()
    {
        $refundApply = $this->refundApply;
        //退款状态设为完成
        $result = RefundOperationService::refundComplete(['order_id' => $refundApply->order->id]);

        if ($result !== true) {
            throw new AdminException($result);
        }
        return $result;
    }

    private function balance()
    {
        $refundApply = $this->refundApply;
        //退款状态设为完成
        RefundOperationService::refundComplete(['order_id' => $refundApply->order->id]);

        $data = [
            'member_id' => $refundApply->uid,
            'remark' => '订单(ID' . $refundApply->order->id . ')余额支付退款(ID' . $refundApply->id . ')' . $refundApply->price,
            'source' => ConstService::SOURCE_CANCEL_CONSUME,
            'relation' => $refundApply->refund_sn,
            'operator' => ConstService::OPERATOR_ORDER,
            'operator_id' => $refundApply->uid,
            'change_value' => $refundApply->price
        ];
        $result = (new BalanceChange())->cancelConsume($data);


        if ($result !== true) {
            throw new AdminException($result);
        }
        return $result;
    }
}