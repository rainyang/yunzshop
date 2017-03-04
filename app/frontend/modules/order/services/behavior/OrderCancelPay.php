<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/2/28
 * Time: 上午11:19
 * comment: 取消支付
 */

namespace app\frontend\modules\order\services\behavior;

use app\common\models\Order;

class OrderCancelPay
{
    public $order_model;

    public function __construct(Order $order_model)
    {
        $this->order_model = $order_model;
    }

    public function cancelPay()
    {
        $this->order_model->status = 0;
        return $this->order_model->save();
    }

    public function cancelable()
    {
        if ($this->order_model['status'] == 1) {
            return true;
        }
        return false;
    }
}