<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/3/3
 * Time: 下午3:43
 */

namespace app\frontend\modules\order\services\behavior;


use app\common\events\order\AfterOrderSentEvent;
use app\common\models\Order;
use Illuminate\Support\Facades\Event;

class OrderSend
{
    public $order_model;

    public function __construct(Order $order_model)
    {
        $this->order_model = $order_model;
    }

    public function send()
    {
        $this->order_model->status = 2;
        $result = $this->order_model->save();
        Event::fire(new AfterOrderSentEvent($this->order_model));
        return $result;
    }

    public function sendable()
    {
        if ($this->order_model->status == 1) {
            return true;
        }
        return false;
    }
}