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
        $this->order_model->status = 1;
        return $this->order_model->save();
    }

    public function cancelSendable()  //todo isValid()?
    {
        if ($this->order_model['status'] == 2) {
            return true;
        }
        return false;
    }
}