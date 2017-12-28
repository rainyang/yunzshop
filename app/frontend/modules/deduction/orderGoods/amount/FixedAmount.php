<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/13
 * Time: 下午5:07
 */

namespace app\frontend\modules\deduction\orderGoods\amount;

/**
 * 固定金额抵扣
 * Class FixedAmount
 * @package app\frontend\modules\deduction\orderGoods\amount
 */
class FixedAmount extends OrderGoodsDeductionAmount
{
    public function getAmount()
    {
        echo '<pre>';print_r(1);exit();
        $result = $this->getGoodsDeduction()->getFixedAmount() * $this->getOrderGoods()->total;
        return max($result, 0);
    }
}