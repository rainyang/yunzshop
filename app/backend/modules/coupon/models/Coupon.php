<?php

namespace app\backend\modules\coupon\models;


class Coupon extends \app\common\models\Coupon
{
    public $table = 'yz_coupon';

    /**
     * @param $keyword
     * @return mixed
     */
    public static function getCouponsByName($keyword)
    {
        return static::uniacid()->select('id', 'name')
            ->where('name', 'like', '%' . $keyword . '%')
            ->get();
    }
}
