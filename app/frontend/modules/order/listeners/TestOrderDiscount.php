<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/11
 * Time: 上午10:00
 */

namespace app\frontend\modules\order\listeners;

use app\common\events\OrderDiscountWasCalculated;

class TestOrderDiscount
{
    public function needDiscount(){
        return true;
    }
    public function getDiscountDetails(){

        $details = [];
        $details[] = [
            'name'=>'订单满减',
            'value'=>'85',
            'price'=>'50',
            'plugin'=>'0',
        ];

        return $details;
    }
    public function handle(OrderDiscountWasCalculated $even)
    {

        if (!$this->needDiscount()) {
            return;
        }
        $even->getOrderModel()->setDiscountDetails($this->getDiscountDetails());

        return;
    }
}