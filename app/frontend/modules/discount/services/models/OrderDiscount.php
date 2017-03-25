<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/15
 * Time: 下午4:29
 */

namespace app\frontend\modules\discount\services\models;

class OrderDiscount extends Discount
{

    // todo 获取订单可选的抵扣
    public function getDeductions()
    {
        $data[] = [
            'id' => 1,
            'name' => '积分抵扣',
            'value' => 20,
            'price' => 20,
            'plugin' => 0
        ];
        return $data;
    }

    // todo 获取订单可选的优惠券
    public function getCoupons()
    {
        $data[] = [
            'id' => 1,
            'name' => '优惠券1',
            'max_value' => 30,
            'max_price' => 30,
            'plugin' => 0
        ];
        return $data;
    }

    // todo 获取订单抵扣金额
    public function getDeductionPrice()
    {
        return 20;
    }

    //todo 获取订单优惠金额
    public function getDiscountPrice()
    {
        return -80;
    }
}