<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/15
 * Time: 下午9:03
 */

namespace app\frontend\modules\deduction;

use Illuminate\Database\Eloquent\Collection;

/**
 * 抵扣设置集合
 * Class DeductionSettingCollection
 * @package app\frontend\modules\deduction
 */
class DeductionSettingCollection extends Collection
{
    /**
     * @return float
     */
    public function getImportantAndValidFixedAmount()
    {
        // todo 按权重排序
        // 获取抵扣设置集合中设置了抵扣金额的,权重最高的设置项
        /**
         * @var DeductionSettingInterface $deductionSetting
         */
        $priceProportion = 0;
        foreach ($this as $deductionSetting){

            if($deductionSetting->isDisable()){
                $priceProportion = 0;
                break;
            }
            if($deductionSetting->getFixedAmount() !== false){
                $priceProportion = $deductionSetting->getFixedAmount();
                break;
            }
        }

        return $priceProportion;
    }

    /**
     * @return float
     */
    public function getImportantAndValidPriceProportion()
    {
        // todo 按权重排序

        // 找到抵扣设置集合中设置了价格比例的,权重最高的设置项

        $priceProportion = 0;
        foreach ($this as $deductionSetting){
            /**
             * @var DeductionSettingInterface $deductionSetting
             */
            if($deductionSetting->isDisable()){
                $priceProportion = 0;
                break;
            }
            if($deductionSetting->getPriceProportion() !== false){
                $priceProportion = $deductionSetting->getPriceProportion();
                break;
            }
        }

        return $priceProportion;
    }

    public function getImportantAndValidCalculationType(){
        // todo 按权重排序

        $type = '';
        foreach ($this as $deductionSetting){
            /**
             * @var DeductionSettingInterface $deductionSetting
             */
            if($deductionSetting->isDisable()){
                break;
            }
            if($deductionSetting->getDeductionType() !== false){
                $type = $deductionSetting->getDeductionType();
                break;
            }
        }

        return $type;
    }
}