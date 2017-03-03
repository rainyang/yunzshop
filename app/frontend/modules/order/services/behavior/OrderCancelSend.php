<?php

namespace app\frontend\modules\order\services\behavior;

use app\common\models\Order;

/*
 * 取消发货
 */
class OrderCancelSend
{
    public $order_model;

    public function __construct(Order $order_model)
    {
        $this->order_model = $order_model->getData();
    }

    public function cancelSend()
    {
        Order::where('shop_id', $this->order_model['shop_id'])
            ->where('id', $this->order_model['id'])
            ->update(['status' => 1]);
    }

    public function cancelSendable()  //todo isValid()?
    {
        if ($this->order_model['status'] == 2) {
            return true;
        } else {
            return false;
        }
    }
}