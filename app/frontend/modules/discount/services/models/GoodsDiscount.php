<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/11
 * Time: 上午10:00
 */

namespace app\frontend\modules\discount\services\models;

class GoodsDiscount extends Discount
{
    public function getDiscountDetails(){
        echo 1;exit;
        $details = [];
        $details[] = [
            'name'=>'折扣',
            'value'=>'85',
            'price'=>'50',
            'plugin'=>'0',
        ];
        $details[] = [
            'name'=>'云币抵扣',
            'value'=>'600',
            'price'=>'60',
            'plugin'=>'2',
        ];
        return $details;
    }

}