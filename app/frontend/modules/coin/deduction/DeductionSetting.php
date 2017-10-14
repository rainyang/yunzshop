<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/13
 * Time: 上午11:01
 */

namespace app\frontend\modules\coin\deduction;

interface DeductionSetting
{
    public function isEnableDeductDispatchPrice();
    public function isEnable();
}