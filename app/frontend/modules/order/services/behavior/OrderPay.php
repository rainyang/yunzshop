<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/2/28
 * Time: 上午10:35
 * comment:订单支付类
 */

namespace app\frontend\modules\order\services\behavior;

use app\common\models\Order;

class OrderPay
{
    public $order_model;

    public function __construct(Order $order_model)
    {
        $this->order_model = $order_model;
    }

    public function pay()
    {
        $this->order_model->status = 1;
        return $this->order_model->save();
    }

    public function payable()
    {
        if ($this->order_model->status == 0) {
            return true;
        }
        return false;
    }
}