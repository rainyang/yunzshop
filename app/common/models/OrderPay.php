<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/2/28
 * Time: 上午10:35
 * comment:订单支付类
 */

namespace app\common\models;

class OrderPay
{
    public $order_model;

    public function __construct(OrderModel $order_model)
    {
        $this->order_model = $order_model->getData();
    }

    public function pay()
    {
        Order::update(['status' => 1])
            ->where('shop_id', '=', $this->order_model['shop_id'])
            ->where('id', '=', $this->order_model['id']);
    }

    public function payable()
    {
        if ($this->order_model['status'] == 0) {
            return true;
        }
    }
}