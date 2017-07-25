<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/7/25
 * Time: 下午7:33
 */

namespace app\frontend\models\order;


class PreOrderCoupon extends \app\common\models\order\OrderCoupon
{
    public $orderDiscount;

    public function setOrderDiscount($orderDiscount)
    {
        $this->orderDiscount = $orderDiscount;
        $orderDiscount->orderDiscount->push($this);
    }
}