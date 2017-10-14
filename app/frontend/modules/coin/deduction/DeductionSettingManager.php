<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/11
 * Time: 上午10:41
 */

namespace app\frontend\modules\coin\deduction;

use Illuminate\Container\Container;
use Yunshop\Love\Frontend\Models\DeductionSetting;

class DeductionSettingManager extends Container
{
    public function __construct()
    {
        /**
         * 积分抵扣设置模型
         */
        $this->bind('point', function ($deductionSettingManager, $attributes = []) {
            return new \app\frontend\modules\finance\deduction\DeductionSetting($attributes);
        });
    }
}