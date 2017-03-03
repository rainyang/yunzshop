<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/2/28
 * Time: 上午11:21
 * comment: 订单删除
 */

namespace app\frontend\modules\order\services\behavior;
use app\common\models\Order;

class OrderDelete
{
    public $order_model;

    public function __construct(Order $order_model)
    {
        $this->order_model = $order_model->getData();
    }

    public function delete()
    {
        return $this->order_model->destroy($this->order_model->id);
    }

    public function deleteable()
    {
        if ($this->order_model['status'] == -1 || $this->order_model['status'] == 3) {
            return true;
        }
        return false;
    }
}