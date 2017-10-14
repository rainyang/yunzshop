<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/13
 * Time: 下午5:05
 */

namespace app\frontend\modules\coin\deduction\orderGoods\amount;

class Proportion extends OrderGoodsDeductionAmount
{
    public function getAmount()
    {
        $result = $this->goodsDeduction->getGoodsDeductionProportion() * $this->orderGoods->goods_price;

        return $result;
    }
    // todo 商品
    // todo 全局
}