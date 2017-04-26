<?php
/**
 * 单订单余额支付
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/17
 * Time: 上午10:57
 */

namespace app\frontend\modules\order\controllers;


use app\common\exceptions\AppException;
use app\common\models\finance\Balance;
use app\common\models\PayType;
use app\common\services\PayFactory;
use app\frontend\modules\order\services\OrderService;
use Illuminate\Support\Collection;

class CreditMergePayController extends MergePayController
{
    public function credit2(\Request $request)
    {
        $result = $this->pay($request, PayFactory::PAY_CREDIT);

        if (!$result) {
            throw new AppException('余额扣除失败,请联系客服');
        }
        $this->orders->each(function($order){
            if (!OrderService::orderPay(['order_id' => $order->id])) {
                throw new AppException('订单状态改变失败,请联系客服');
            }
        });

        return $this->successJson('成功', []);
    }

    protected function getPayParams($orderPay, Collection $orders)
    {
        $result = [
            'member_id' => $orders->first()->uid,
            'operator' => Balance::OPERATOR_ORDER_,//订单
            'operator_id' => $orderPay->id,
            'remark' => '合并支付(id:' . $orderPay->id . '),余额付款' . $orderPay->amount . '元',
            'service_type' => Balance::BALANCE_CONSUME,
            ];

        return array_merge(parent::getPayParams($orderPay,$orders), $result);
    }
}