<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/10
 * Time: 下午6:05
 */

namespace app\frontend\modules\coupon\services;


class MemberCouponService
{
    static private $memberCoupons;

    public static function getStaticCurrentMemberCoupon($member)
    {
        if(!isset(self::$memberCoupons)){
            return self::$memberCoupons = self::getCurrentMemberCoupon($member);
        }
        return self::$memberCoupons;

    }

    public static function getCurrentMemberCoupon($member)
    {

        return $member->hasManyMemberCoupon()->get();
    }
}