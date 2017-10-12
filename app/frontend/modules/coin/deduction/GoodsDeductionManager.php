<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/11
 * Time: 上午10:41
 */

namespace app\frontend\modules\coin\deduction;

use Illuminate\Container\Container;
use Yunshop\Love\Frontend\Models\GoodsLoveDeduction;

class GoodsDeductionManager extends Container
{
    public function __construct()
    {
        $this->bind('love',function($goodsDeductionManager,$attribute = []){
            return new GoodsLoveDeduction($attribute);
        });
    }
}