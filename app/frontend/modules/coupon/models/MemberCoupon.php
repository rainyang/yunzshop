<?php

namespace app\frontend\modules\coupon\models;


class MemberCoupon extends \app\common\models\MemberCoupon
{
    public $table = 'yz_member_coupon';
    const USED = 1;
    const NOT_USED = 0;

    //获取指定用户名下的优惠券
    public static function getCouponsOfMember($memberId)
    {
        $coupons = static::uniacid()->with(['belongsToCoupon' => function($query){
            return $query->select(['id', 'name', 'coupon_method','deduct', 'discount', 'enough', 'use_type', 'categorynames',
                                    'goods_names', 'time_limit', 'time_days', 'time_start', 'time_end', 'total',
                                    'money', 'credit']);
        }])->where('uid', $memberId)->select(['id', 'coupon_id', 'used', 'get_time']);
        return $coupons;
    }
}
