<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/2/28
 * Time: 上午11:07
 * comment:订单关闭类
 */

namespace app\frontend\modules\order\services\behavior;
use app\common\models\Order;

class OrderClose
{
    public $order_model;

    public function __construct(Order $order_model)
    {
        $this->order_model = $order_model->getData();
    }

    public function close()
    {
        Order::update(['status' => -1])
            ->where('shop_id', '=', $this->order_model['shop_id'])
            ->where('id', '=', $this->order_model['id']);
    }

    public function closeable()
    {
        if ($this->order_model['status'] == 0) {
            return true;
        }
    }
}