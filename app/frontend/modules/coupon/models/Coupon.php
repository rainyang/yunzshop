<?php

namespace app\frontend\modules\coupon\models;


class Coupon extends \app\common\models\Coupon
{
    public $table = 'yz_coupon';

    public static function getCoupons()
    {
        return static::uniacid()
                        ->where('status', '=', 1)
                        ->get();
    }
}
