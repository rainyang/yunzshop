<?php

namespace app\frontend\modules\coupon\models;


class MemberCoupon extends \app\common\models\MemberCoupon
{
    public $table = 'yz_member_coupon';

    //获取指定用户名下的优惠券
    public static function getCouponsOfMember($memberId, $param = [])
    {
        $rawCoupons = static::with(['belongsToCoupon'])->where('uid', $memberId);
        return $rawCoupons;
    }

}
