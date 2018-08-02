<?php

namespace app\common\modules\refund\services;

use app\backend\modules\refund\models\RefundApply;
use app\backend\modules\refund\services\RefundOperationService;
use app\common\exceptions\AdminException;
use app\common\models\Order;
use app\common\models\PayType;
use app\common\services\credit\ConstService;
use app\common\services\finance\BalanceChange;
use app\common\services\PayFactory;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/10
 * Time: 下午4:29
 */
class RefundService
{
    protected $refundApply;
    public function fastRefund($order_id){
        $order = Order::find($order_id);
        $refundApply = \app\common\models\refund\RefundApply::createByOrder($order);
        $refundApply->save();
        return $this->pay($refundApply->id);
    }
    public function pay($refund_id)
    {
        $this->refundApply = RefundApply::find($refund_id);

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
            case PayType::WechatApp:
                $result = $this->wechat();
                break;
            case PayType::AlipayApp:
                $result = $this->alipayapp();
                break;
            case PayType::PAY_YUN_WECHAT:
                $result = $this->yunWechat();
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
        RefundOperationService::refundComplete(['id' => $this->refundApply->id]);
        $pay = PayFactory::create($this->refundApply->order->pay_type_id);
        //dd([$this->refundApply->order->hasOneOrderPay->pay_sn, $this->refundApply->order->hasOneOrderPay->amount, $this->refundApply->price]);

        $result = $pay->doRefund($this->refundApply->order->hasOneOrderPay->pay_sn, $this->refundApply->order->hasOneOrderPay->amount, $this->refundApply->price);

        if (!$result) {
            throw new AdminException('微信退款失败');
        }
        return $result;
    }

    private function alipay()
    {
        //RefundOperationService::refundComplete(['id' => $this->refundApply->id]);

        $pay = PayFactory::create($this->refundApply->order->pay_type_id);

        $result = $pay->doRefund($this->refundApply->order->hasOneOrderPay->pay_sn, $this->refundApply->order->hasOneOrderPay->amount, $this->refundApply->price);

        if ($result === false) {
            throw new AdminException('支付宝退款失败');
        }
        //保存batch_no,回调成功后根据batch_no找到对应的退款记录
        $this->refundApply->alipay_batch_sn = $result['batch_no'];
        $this->refundApply->save();
        return $result['url'];
    }

    private function alipayapp()
    {
        RefundOperationService::refundComplete(['id' => $this->refundApply->id]);

        $pay = PayFactory::create($this->refundApply->order->pay_type_id);

        $result = $pay->doRefund($this->refundApply->order->hasOneOrderPay->pay_sn, $this->refundApply->order->hasOneOrderPay->amount, $this->refundApply->price);

        if ($result === false) {
            throw new AdminException('支付宝退款失败');
        }
        return $result;
    }

    private function backend()
    {
        $refundApply = $this->refundApply;
        //退款状态设为完成
        $result = RefundOperationService::refundComplete(['id' => $refundApply->id]);

        if ($result !== true) {
            throw new AdminException($result);
        }
        return $result;
    }

    private function balance()
    {
        $refundApply = $this->refundApply;
        //退款状态设为完成
        RefundOperationService::refundComplete(['id' => $refundApply->id]);

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

    private function yunWechat()
    {
        //芸支付微信退款 同步改变退款和订单状态
        RefundOperationService::refundComplete(['id' => $this->refundApply->id]);
        $pay = PayFactory::create($this->refundApply->order->pay_type_id);

        $result = $pay->doRefund($this->refundApply->order->hasOneOrderPay->pay_sn, $this->refundApply->order->hasOneOrderPay->amount, $this->refundApply->price);

        if (!$result) {
            throw new AdminException('芸支付微信退款失败');
        }
        return $result;
    }
}