<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/13
 * Time: 下午1:49
 */

namespace app\frontend\modules\finance\deduction;


class DeductionSetting implements \app\frontend\modules\coin\deduction\DeductionSetting
{
    public function isEnableDeductDispatchPrice()
    {
        return false;
    }
    public function isEnable(){
        return \Setting::get('point.set.point_deduct');
    }
}