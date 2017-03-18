<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/9
 * Time: 下午1:53
 */

namespace app\common\events\discount;
use app\common\events\Event;
use app\frontend\modules\discount\services\models\OrderDiscount;


class OrderDiscountWasCalculated extends Event
{
    private $orderDiscount;

    public function __construct(OrderDiscount $OrderDiscount)
    {
        $this->orderDiscount = $OrderDiscount;
    }
    public function getOrderDiscount(){
        return $this->orderDiscount;
    }
    public function getOrderModel(){
        return $this->getOrderModel();

    }
}