<?php
/**
 * 单订单余额支付
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/17
 * Time: 上午10:57
 */

namespace app\frontend\modules\order\controllers;

use app\common\events\payment\ChargeComplatedEvent;
use app\common\exceptions\AppException;
use app\common\services\password\PasswordService;
use app\common\services\PayFactory;
use app\frontend\models\OrderPay;

class CreditMergePayController extends MergePayController
{
    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     * @throws \app\common\exceptions\PaymentException
     * @throws \app\common\exceptions\ShopException
     */
    public function credit2()
    {
        if (\Setting::get('shop.pay.credit') == false) {
            throw new AppException('商城未开启余额支付');

        }
        $this->checkPassword(\YunShop::app()->getMemberId());

        /**
         * @var OrderPay $orderPay
         */
        $orderPay = OrderPay::find(request()->input('order_pay_id'));
        $result = $orderPay->getPayResult(PayFactory::PAY_CREDIT);

        if (!$result) {
            throw new AppException('余额扣除失败,请联系客服');
        }

        $orderPay->pay();

        event(new ChargeComplatedEvent([
            'order_pay_id' => $orderPay->id
        ]));

        $trade = \Setting::get('shop.trade');
        $redirect = '';

        if (!is_null($trade) && isset($trade['redirect_url']) && !empty($trade['redirect_url'])) {
            $redirect = $trade['redirect_url'];
        }

        return $this->successJson('成功', ['redirect' => $redirect]);
    }

    /**
     * 校验支付密码
     * @param $uid
     * @return bool
     * @throws \app\common\exceptions\PaymentException
     * @throws \app\common\exceptions\ShopException
     */
    private function checkPassword($uid)
    {
        if (!\Setting::get('shop.pay.balance_pay_proving')) {
            // 未开启
            return true;
        }
        $this->validate([
            'payment_password' => 'required'
        ]);
        return (new PasswordService())->checkMemberPassword($uid, request()->input('payment_password'));
    }
}