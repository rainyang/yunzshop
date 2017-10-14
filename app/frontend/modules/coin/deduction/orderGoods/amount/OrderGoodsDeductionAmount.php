<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/13
 * Time: 下午5:10
 */

namespace app\frontend\modules\coin\deduction\orderGoods\amount;


use app\frontend\modules\coin\deduction\GoodsDeduction;
use app\frontend\modules\orderGoods\models\PreOrderGoods;

abstract class OrderGoodsDeductionAmount
{
    protected $orderGoods;
    protected $goodsDeduction;
    function __construct(PreOrderGoods $orderGoods,GoodsDeduction $goodsDeduction)
    {
        $this->orderGoods = $orderGoods;
        $this->goodsDeduction = $goodsDeduction;
    }

    abstract public function getAmount();
}