<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/7/25
 * Time: 下午7:33
 */

namespace app\frontend\models\order;


use app\frontend\modules\order\models\PreGeneratedOrder;

class PreOrderCoupon extends \app\common\models\order\OrderCoupon
{
    public $order;

    public function setOrder(PreGeneratedOrder $order)
    {
        $this->order = $order;
        $this->uid = $order->uid;
        $order->orderCoupons->push($this);
    }
}