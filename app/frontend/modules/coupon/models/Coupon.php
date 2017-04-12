<?php

namespace app\frontend\modules\coupon\models;


class Coupon extends \app\common\models\Coupon
{
    public $table = 'yz_coupon';

    const RELATIVE_TIME_LIMIT_TYPE = 0;
    const ABSOLUTE_TIME_LIMIT_TYPE = 1;

    public static function getCouponsForMember($memberId)
    {
        return static::uniacid()
                        ->withCount(['hasManyMemberCoupon'])
                        ->withCount(['hasManyMemberCoupon as member_got' => function($query) use($memberId){
                            return $query->where('uid', '=', $memberId);
                        }])
                        ->where('status', '=', 1);
    }
}
