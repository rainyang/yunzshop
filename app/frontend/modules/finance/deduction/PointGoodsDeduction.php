<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/12
 * Time: 下午3:28
 */

namespace app\frontend\modules\finance\deduction;

use app\frontend\modules\deduction\GoodsDeduction;

class PointGoodsDeduction extends GoodsDeduction
{
    // todo 有问题,先实现
    public function getFixedAmount()
    {
        return $this->getDeductionSettingCollection()->getImportantAndValidFixedAmount();
    }

    // todo 有问题,先实现
    public function getPriceProportion()
    {
        return $this->getDeductionSettingCollection()->getImportantAndValidPriceProportion();
    }

    // todo 有问题,先实现
    public function getDeductionAmountCalculationType()
    {
        echo '<pre>';print_r($this->getDeductionSettingCollection()->getImportantAndValidCalculationType());exit();
        return $this->getDeductionSettingCollection()->getImportantAndValidCalculationType();
    }

    public function deductible($goods)
    {
        return true;
    }
}