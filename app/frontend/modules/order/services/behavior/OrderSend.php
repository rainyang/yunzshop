<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/3/3
 * Time: 下午3:43
 */

namespace app\frontend\modules\order\services\behavior;


use app\common\models\Order;

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
        return $this->order_model->save();
    }

    public function sendable()
    {
        if ($this->order_model->status == 1) {
            return true;
        }
        return false;
    }
}