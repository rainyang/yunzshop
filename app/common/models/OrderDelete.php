<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/2/28
 * Time: 上午11:21
 * comment: 订单删除
 */

namespace app\common\models;
class OrderDelete
{
    public $order_model;

    public function __construct(OrderModel $order_model)
    {
        $this->order_model = $order_model->getData();
    }

    public function delete()
    {
        Order::where('shop_id', '=', $this->order_model['shop_id'])
            ->where('id', '=', $this->order_model['id'])
            ->delete();
    }

    public function deleteable()
    {
        if ($this->order_model['status'] == -1 || $this->order_model['status'] == 3) {
            return true;
        }
    }
}