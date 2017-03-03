<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/2/28
 * Time: 上午10:57
 * comment:订单收货类
 */

namespace app\common\models;

class OrderReceive
{
    public $order_model;

    public function __construct(OrderModel $order_model)
    {
        $this->order_model = $order_model->getData();
    }

    public function receive()
    {
        Order::update(['status' => 3])
            ->where('shop_id', '=', $this->order_model['shop_id'])
            ->where('id', '=', $this->order_model['id']);
    }

    public function receiveable()
    {
        if ($this->order_model['status'] == 2) {
            return true;
        }
    }
}