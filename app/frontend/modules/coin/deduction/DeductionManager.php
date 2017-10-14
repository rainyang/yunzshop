<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/11
 * Time: 上午10:41
 */

namespace app\frontend\modules\coin\deduction;

use Illuminate\Container\Container;

class DeductionManager extends Container
{
    public function __construct()
    {
        $this->singleton('GoodsDeductionManager', function ($deductionManager, $attributes = []) {
            return new GoodsDeductionManager($attributes);
        });
        $this->singleton('DeductionSettingManager', function ($deductionManager, $attributes = []) {
            return new DeductionSettingManager($attributes);
        });
    }
}