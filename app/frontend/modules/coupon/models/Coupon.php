<?php

namespace app\frontend\modules\coupon\models;


class Coupon extends \app\common\models\Coupon
{
    public $table = 'yz_coupon';

    const RELATIVE_TIME_LIMIT = 0;
    const ABSOLUTE_TIME_LIMIT = 1;

    public static function getCouponsForMember($memberId)
    {
        return static::uniacid()
                        ->select(['id', 'name', 'deduct', 'discount', 'enough', 'use_type',
                                'categorynames', 'goods_names', 'time_limit', 'time_days', 'time_end', 'get_max', 'total',
                                'money', 'credit'])
                        ->withCount(['hasManyMemberCoupon'])
                        ->withCount(['hasManyMemberCoupon as member_got' => function($query) use($memberId){
                            return $query->where('uid', '=', $memberId);
                        }])
                        ->where('status', '=', 1);
    }
}
