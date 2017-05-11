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
            return $query->select(['id', 'name', 'coupon_method','deduct', 'discount', 'enough', 'use_type', 'category_ids', 'categorynames',
                                    'goods_ids', 'goods_names', 'time_limit', 'time_days', 'time_start', 'time_end', 'total',
                                    'money', 'credit']);
        }])->where('uid', $memberId)
            ->select(['id', 'coupon_id', 'used', 'use_time', 'get_time'])
            ->orderBy('get_time','desc');
        return $coupons;
    }

    //获取用户名下指定优惠券的总数
    public static function getMemberCouponCount($memberId, $couponId)
    {
        $count = static::uniacid()
                    ->where('uid', '=', $memberId)
                    ->where('coupon_id', '=', $couponId)
                    ->count();
        return $count;
    }

    //获取指定优惠券的领取总数
    public static function getTotalGetCount($couponId)
    {
        return static::uniacid()
                    ->where('coupon_id', '=', $couponId)
                    ->count();
    }

    //查找指定ID的用户优惠券
    public static function getById($id)
    {
        return static::uniacid()
                ->find($id);
    }

}
