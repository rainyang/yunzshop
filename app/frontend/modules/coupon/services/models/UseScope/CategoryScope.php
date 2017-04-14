<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/28
 * Time: 下午2:44
 */

namespace app\frontend\modules\coupon\services\models\UseScope;


class CategoryScope extends CouponUseScope
{
    public function valid()
    {
        return false;
    }
}