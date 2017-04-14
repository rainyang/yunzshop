<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/10
 * Time: 下午6:05
 */

namespace app\frontend\modules\coupon\services;


use Illuminate\Support\Collection;

class MemberCouponService
{
    static private $memberCoupons;

    public static function getCurrentMemberCouponCache($member)
    {
        if(!isset(self::$memberCoupons)){
            return self::$memberCoupons = self::getCurrentMemberCoupon($member);
        }
        return self::$memberCoupons;

    }
    /**
     * @param $member
     * @return Collection
     */
    public static function getCurrentMemberCoupon($member)
    {
        return $member->hasManyMemberCoupon()->get();
    }
}