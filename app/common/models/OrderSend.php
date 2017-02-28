<?php

namespace app\common\models;

use Illuminate\Database\Eloquent\Model;
use app\common\models\Order;

/*
 * 确认发货
 */
class OrderSent
{
    public $order_model;

    public function __construct(OrderModel $order_model)
    {
        $this->order_model = $order_model->getData();
    }

    public function send()
    {
        Order::where('shop_id', $this->order_model['shop_id'])
            ->where('id', $this->order_model['id'])
            ->update(['status' => 2]);
    }

    public function sendable() //todo isValid()?
    {
        if ($this->order_model['status'] == 1) {
            return true;
        } else {
            return false;
        }
    }
}