<?php

namespace app\frontend\modules\order\services\behavior;

use app\common\events\order\AfterOrderCancelSentEvent;
use app\common\models\Order;
use Illuminate\Support\Facades\Event;

/*
 * 取消发货
 */
class OrderCancelSend
{
    public $order_model;

    public function __construct(Order $order_model)
    {
        $this->order_model = $order_model;
    }

    public function cancelSend()
    {
        $this->order_model->status = 1;
        $result = $this->order_model->save();
        Event::fire(new AfterOrderCancelSentEvent($this->order_model));
        return $result;
    }

    public function cancelSendable()  //todo isValid()?
    {
        if ($this->order_model->status == 2) {
            return true;
        }
        return false;
    }
}