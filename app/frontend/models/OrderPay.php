<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/6
 * Time: 下午3:41
 */

namespace app\frontend\models;


use app\common\exceptions\AppException;
use app\common\services\PayFactory;

class OrderPay extends \app\common\models\OrderPay
{
    /**
     * @param $payType
     * @return mixed
     * @throws AppException
     */
    public function getPayResult($payType, $payParams = [])
    {

        if ($this->status > 0) {
            throw new AppException('(ID' . $this->id . '),此流水号已支付');
        }
        if ($this->orders->isEmpty()) {
            throw new AppException('(ID:' . $this->id . ')未找到对应订单');
        }
        $this->orders->each(function (\app\common\models\Order $order) {
            if ($order->status > Order::WAIT_PAY) {
                throw new AppException('(ID:' . $order->id . ')订单已付款,请勿重复付款');
            }
            if ($order->status == Order::CLOSE) {
                throw new AppException('(ID:' . $order->id . ')订单已关闭,无法付款');
            }
        });
        $query_str = $this->getPayParams($payParams);
        $pay = PayFactory::create($payType);
        //如果支付模块常量改变 数据会受影响
        $result = $pay->doPay($query_str, $payType);

        if (!isset($result)) {
            throw new AppException('获取支付参数失败');
        }
        return $result;
    }

    /**
     * @param $option
     * @return array
     * @throws AppException
     */
    protected function getPayParams($option)
    {
        $extra = ['type' => 1];

        if (!is_array($option)) {
            throw new AppException('参数类型错误');
        }

        $extra = array_merge($extra, $option);

        return [
            'order_no' => $this->pay_sn,
            'amount' => $this->orders->sum('price'),
            'subject' => $this->orders->first()->hasManyOrderGoods[0]->title ?: '芸众商品',
            'body' => ($this->orders->first()->hasManyOrderGoods[0]->title ?: '芸众商品') . ':' . \YunShop::app()->uniacid,
            'extra' => $extra
        ];
    }
}