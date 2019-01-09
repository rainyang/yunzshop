<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/13
 * Time: 上午11:01
 */

namespace app\frontend\modules\deduction;

/**
 * 商品抵扣基类
 * Class GoodsDeduction
 * @package app\frontend\modules\deduction
 */
abstract class GoodsDeduction
{
    protected $deductionSettingCollection;
    function __construct(DeductionSettingCollection $deductionSettingCollection)
    {
        $this->deductionSettingCollection = $deductionSettingCollection;
    }

    /**
     * @return DeductionSettingCollection
     */
    public function getDeductionSettingCollection()
    {
        return $this->deductionSettingCollection;
    }

    /**
     * 获取商品可以抵扣的价格比例
     * @return float
     */
    abstract public function getPriceProportion();

    /**
     * 获取商品可以抵扣的固定金额
     * @return float
     */
    abstract public function getFixedAmount();

    /**
     * 获取抵扣金额计算方式
     * @return string
     */
    abstract public function getDeductionAmountCalculationType();

    /**
     * 商品可使用抵扣
     * @param $goods
     * @return bool
     */
    abstract public function deductible($goods);

}