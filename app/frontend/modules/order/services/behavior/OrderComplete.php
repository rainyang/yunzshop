<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/2/28
 * Time: 上午11:12
 * comment: 订单完成
 */

namespace app\frontend\modules\order\services\behavior;
use app\common\models\Order;

class OrderComplete
{
    public $order_model;

    public function __construct(Order $order_model)
    {
        $this->order_model = $order_model;
    }

    public function completeable()
    {
        if ($this->order_model->status == 3) {
            return true;
        }
    }
}