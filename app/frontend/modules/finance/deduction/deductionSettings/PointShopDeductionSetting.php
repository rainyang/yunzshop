<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/13
 * Time: 下午1:49
 */

namespace app\frontend\modules\finance\deduction\deductionSettings;

use app\frontend\modules\deduction\DeductionSettingInterface;

class PointShopDeductionSetting implements DeductionSettingInterface
{
    public function getWeight()
    {
        return 30;
    }
    public function isEnableDeductDispatchPrice()
    {
        return true;
    }

    public function isDisable()
    {
        return !\Setting::get('point.set.point_deduct');
    }

    public function getFixedAmount()
    {
        return false;
    }

    public function getPriceProportion()
    {
        return \Setting::get('point.set.money_max');
    }
    public function getDeductionType(){
        return 'GoodsPriceProportion';
    }
}