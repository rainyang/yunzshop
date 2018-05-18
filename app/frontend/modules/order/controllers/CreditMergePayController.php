<?php
/**
 * 单订单余额支付
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/17
 * Time: 上午10:57
 */

namespace app\frontend\modules\order\controllers;


use app\backend\modules\member\models\MemberRelation;
use app\common\events\payment\ChargeComplatedEvent;
use app\common\exceptions\AppException;
use app\common\models\finance\Balance;
use app\common\services\password\PasswordService;
use app\common\services\PayFactory;
use app\frontend\modules\order\services\OrderService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CreditMergePayController extends MergePayController
{
    public function credit2(\Request $request)
    {
        if (\Setting::get('shop.pay.credit') == false) {
            throw new AppException('商城未开启余额支付');

        }
        $this->checkPassword(\YunShop::app()->getMemberId());

        DB::transaction(function () {
            $result = $this->pay(PayFactory::PAY_CREDIT);

            if (!$result) {
                throw new AppException('余额扣除失败,请联系客服');
            }
            //todo 临时解决 需要重构
            $this->orderPay->pay_type_id = PayFactory::PAY_CREDIT;
            $this->orderPay->status = 1;
            $this->orderPay->save();
            $this->orders->each(function ($order) {
                if (!OrderService::orderPay(['order_id' => $order->id, 'order_pay_id' => $this->orderPay->id, 'pay_type_id' => PayFactory::PAY_CREDIT])) {
                    throw new AppException('订单状态改变失败,请联系客服');
                }
            });

            event(new ChargeComplatedEvent([
                'order_pay_id' => $this->orderPay->id
            ]));

            //会员推广资格
            \Log::debug('余额支付-会员推广');
            MemberRelation::checkOrderPay($this->orderPay->uid);
        });

        $trade = \Setting::get('shop.trade');
        $redirect = '';

        if (!is_null($trade) && isset($trade['redirect_url']) && !empty($trade['redirect_url'])) {
            $redirect = $trade['redirect_url'];
        }
        \Log::debug('------credit----', $redirect);
        return $this->successJson('成功', ['redirect'=>$redirect]);
    }

    protected function getPayParams($orderPay, Collection $orders)
    {
        $result = [
            'member_id' => $orders->first()->uid,
            'operator' => Balance::OPERATOR_ORDER_,//订单
            'operator_id' => $orderPay->id,
            'remark' => '合并支付(id:' . $orderPay->id . '),余额付款' . $orderPay->amount . '元',
            'service_type' => Balance::BALANCE_CONSUME,
            'trade_no' => 0,
        ];

        return array_merge(parent::getPayParams($orderPay, $orders), $result);
    }
    /**
     * 校验支付密码
     * @param $uid
     * @return bool
     */
    private function checkPassword($uid){
        if(!\Setting::get('shop.pay.balance_pay_proving')){
            // 未开启
            return true;
        }
        $this->validate([
            'payment_password' => 'required|string'
        ]);
        return (new PasswordService())->checkMemberPassword($uid,request()->input('payment_password'));
    }
}