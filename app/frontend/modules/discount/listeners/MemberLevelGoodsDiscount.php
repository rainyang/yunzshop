<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/11
 * Time: 上午10:00
 */

namespace app\frontend\modules\discount\listeners;

use app\common\events\discount\OrderGoodsDiscountWasCalculated;

class MemberLevelGoodsDiscount
{
    public function needDiscount(){
        return true;
    }
    public function getDiscountDetails(){
        $details = [];
        $details[] = [
            'name'=>'会员等级折扣',
            'value'=>'85',
            'price'=>'50',
            'plugin'=>'0',
        ];

        return $details;
    }
    public function handle(OrderGoodsDiscountWasCalculated $even)
    {

        if (!$this->needDiscount()) {
            return;
        }

        $even->getOrderGoodsModel()->setDiscountDetails($this->getDiscountDetails());

        return;
    }
}