<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/2/28
 * Time: 上午10:57
 * comment:订单收货类
 */

namespace app\frontend\modules\order\services\behavior;

use app\common\models\Order;

class OrderReceive
{
    public $order_model;

    public function __construct(Order $order_model)
    {
        $this->order_model = $order_model;
    }

    public function receive()
    {
        $this->order_model->status = 3;
        return $this->order_model->save();
    }

    public function receiveable()
    {
        if ($this->order_model->status == 2) {
            return true;
        }
        return false;
    }
}