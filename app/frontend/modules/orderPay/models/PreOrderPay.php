<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/6
 * Time: 下午3:41
 */

namespace app\frontend\modules\orderPay\models;

use app\common\exceptions\AppException;
use app\frontend\models\OrderPay;
use app\frontend\modules\order\OrderCollection;
use app\frontend\modules\order\services\OrderService;

class PreOrderPay extends OrderPay
{
    public function setOrders(OrderCollection $orders)
    {
        $this->order_ids = $orders->pluck('id');
        $this->amount = $orders->sum('price');
        $this->uid = $orders->first()->uid;
        $this->pay_sn = OrderService::createPaySN();
    }

    /**
     * @throws AppException
     */
    public function store()
    {
        $this->save();
        if ($this->id === null) {
            throw new AppException('支付流水记录保存失败');

        }
        $this->orders()->attach($this->order_ids);
    }
}