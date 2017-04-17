<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/17
 * Time: 上午10:57
 */

namespace app\frontend\modules\order\controllers;


use app\common\exceptions\AppException;
use app\common\models\finance\Balance;
use app\common\services\PayFactory;
use app\frontend\modules\order\models\Order;
use app\frontend\modules\order\services\OrderService;

class CreditPayController extends PayController
{
    public function credit2(\Request $request)
    {
        $result = $this->pay($request, PayFactory::PAY_CREDIT);
        
        if (!$result) {
            throw new AppException('余额扣除失败,请联系客服');
        }
        if (!OrderService::orderPay(['order_id' => $this->order()->id])) {
            throw new AppException('订单状态改变失败,请联系客服');
        }

        return $this->successJson('成功', []);
    }

    protected function pay($request, $payType)
    {
        $this->_validate($request);
        $this->order = Order::find($request->query('order_id'));

        $query_str = [
            'order_no' => $this->order()->order_sn,
            'amount' => -($this->order()->price),
            'subject' => '微信支付',
            'body' => $this->order()->hasManyOrderGoods[0]->title . ':' . \YunShop::app()->uniacid,
            'extra' => ['type' => 1],
            'member_id' => $this->order()->uid,
            'operator' => '-1',//订单
            'operator_id' => $this->order()->id,
            'remark' => '订单(id:' . $this->order()->id . '),余额支付' . $this->order()->price . '元',
            'service_type' => Balance::BALANCE_CONSUME,
        ];
        $pay = PayFactory::create($payType);
        return $pay->doPay($query_str);
    }
}