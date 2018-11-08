<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/13
 * Time: 下午5:05
 */

namespace app\frontend\modules\deduction\orderGoods\amount;

/**
 * 按比例抵扣金额
 * Class Proportion
 * @package app\frontend\modules\deduction\orderGoods\amount
 */
class GoodsPriceProportion extends OrderGoodsDeductionAmount
{
    public function getAmount()
    {
        $result = $this->getGoodsDeduction()->getPriceProportion() * $this->orderGoods->getPaymentAmount() / 100;

        return max($result,0);
    }
}