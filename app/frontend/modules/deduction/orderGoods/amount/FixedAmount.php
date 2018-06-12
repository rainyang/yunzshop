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
    /**
     * @return float|mixed
     * @throws \app\common\exceptions\ShopException
     */
    public function getAmount()
    {
        $result = $this->getGoodsDeduction()->getFixedAmount() * $this->getOrderGoods()->total;
        $result = min($result,$this->getOrderGoods()->getPrice());
        return max($result, 0);
    }
}