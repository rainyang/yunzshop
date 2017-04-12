<?php
namespace app\frontend\modules\coupon\controllers;

use app\common\components\BaseController;
use app\frontend\modules\coupon\models\Coupon;
use app\frontend\modules\coupon\models\MemberCoupon;
use app\common\models\Member;


class MemberCouponController extends BaseController
{
    //获取用户所有的优惠券 - 1. 已使用, 2. 已过期(超过起止时间 / 超过领取后有效时间), 3. 其它(即可使用)
    public function couponsOfMember()
    {
        $uid = \YunShop::request()->get('test_uid'); //todo
        $member = Member::getMemberById($uid);
        $memberCoupon = new MemberCoupon;

        $coupons = $memberCoupon::getMemberCoupon($member);
        dd($coupons);
//        return $this->successJson('ok', $coupons);
    }

    //提供给用户"优惠券中心"的数据
    public function couponsForMember()
    {
        $coupons = Coupon::getCoupons();

        return $this->successJson('ok', $coupons);
    }
}

