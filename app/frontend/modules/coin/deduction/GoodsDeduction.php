<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/13
 * Time: 上午11:01
 */

namespace app\frontend\modules\coin\deduction;

interface GoodsDeduction
{
    /**
     * 获取商品可以抵扣的比例
     * @return float
     */
    public function getGoodsDeductionProportion();
}