<?php

namespace app\frontend\modules\coupon\services;

use app\common\models\Order;
use app\frontend\modules\coupon\services\models\Coupon;
use app\frontend\modules\coupon\services\models\DiscountCoupon;
use app\frontend\modules\coupon\services\models\MoneyOffCoupon;
use app\frontend\modules\order\services\models\PreGeneratedOrderModel;
use Illuminate\Support\Collection;

class TestService
{
    private $order;
    private $back_type = null;

    public function __construct(PreGeneratedOrderModel $order, $back_type = null)
    {

        $this->order = $order;
        $this->back_type = $back_type;

    }

    /**
     * 获取订单优惠金额
     * @return int
     */
    public function getOrderDiscountPrice()
    {
        return $this->getAllValidCoupons()->sum(function($coupon){
            /**
             * @var $coupon Coupon
             */
            $coupon->activate();
            return $coupon->getDiscountPrice();
        });
    }

    /**
     * 获取订单可算的优惠券
     * @return Collection
     */
    public function getOptionalCoupons()
    {
        $coupons = $this->getMemberCoupon()->map(function ($memberCoupon){
            return new Coupon($memberCoupon, $this->order);
        });
        $result = $coupons->filter(function($coupon){
            return $coupon->valid();
        });
        return $result;
    }

    /**
     * 记录使用过的优惠券
     */
    public function destroyUsedCoupons()
    {
        foreach ($this->getAllValidCoupons() as $coupon){
            /**
             * @var $coupon Coupon
             */
            $coupon->destroy();
        }
    }

    /**
     * 获取所有选中并有效的优惠券
     * @return Collection
     */
    public function getAllValidCoupons()
    {
        $coupon = $this->getSelectedMemberCoupon()->map(function ($memberCoupon){
            return new Coupon($memberCoupon, $this->order);
        });
        $result = $coupon->filter(function($coupon){
            return $coupon->valid();
        });

        return $result;
    }

    /**
     * 用户拥有的优惠券
     * @return Collection
     */
    private function getMemberCoupon()
    {
        $back_type = $this->back_type;
        $result = MemberCouponService::getCurrentMemberCouponCache($this->order->belongsToMember)->filter(function ($memberCoupon) use($back_type){
            return $memberCoupon->belongsToCoupon->back_type == $back_type;
        });
        return $result;

    }
    /**
     * 用户拥有并选中的优惠券
     * @return Collection
     */
    private function getSelectedMemberCoupon()
    {
        $member_coupon_ids = explode(',', array_get($_GET, 'member_coupon_ids', ''));
        return $this->getMemberCoupon()->filter(function ($memberCoupon) use ($member_coupon_ids){
            return in_array($memberCoupon->id, $member_coupon_ids);
        });
    }
}