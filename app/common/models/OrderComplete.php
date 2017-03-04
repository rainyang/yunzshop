<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/2/28
 * Time: 上午11:12
 * comment: 订单完成
 */

namespace app\common\models;
class OrderComplete
{
    public $order_model;

    public function __construct(OrderModel $order_model)
    {
        $this->order_model = $order_model->getData();
    }

    public function complete()
    {
        $this->order_model->status = 3;
        $this->order_model->save();
    }

    public function completeable()
    {
        if ($this->order_model['status'] == 2) {
            $this->complete();
        }
    }
}