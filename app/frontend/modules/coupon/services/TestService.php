<?php

namespace app\frontend\modules\coupon\services;

use app\frontend\modules\coupon\services\models\Coupon;
use app\frontend\modules\coupon\services\models\DiscountCoupon;
use app\frontend\modules\coupon\services\models\MoneyOffCoupon;
use app\frontend\modules\order\services\models\PreGeneratedOrderModel;
use Illuminate\Support\Collection;

class TestService
{
    private $order;
    private $coupon_method = null;

    public function __construct(PreGeneratedOrderModel $order, $coupon_method = null)
    {

        $this->order = $order;
        $this->coupon_method = $coupon_method;
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
        //dd(MemberCouponService::getCurrentMemberCouponCache($this->order->belongsToMember));
        //dd($this->getMemberCoupon());
        $coupons = $this->getMemberCoupon()->map(function ($memberCoupon){
            return new Coupon($memberCoupon, $this->order);
        });
        $result = $coupons->filter(function($coupon){
            //exit;
            /**
             * @var $coupon Coupon
             */
            $result = $coupon->isOptional();

            $coupon->getMemberCoupon()->valid = $coupon->valid();
            $coupon->getMemberCoupon()->checked = $coupon->isChecked();
//            if($result){
//                dd($coupon->getMemberCoupon()->id);
//                dd($coupon->getMemberCoupon()->valid = $coupon->valid());
//            }
            return $result;
        });
//        dd($result);
//        exit;
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
            /**
             * @var $coupon Coupon
             */
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
        $coupon_method = $this->coupon_method;
        $result = MemberCouponService::getCurrentMemberCouponCache($this->order->belongsToMember);
        if(isset($coupon_method)){// 折扣/立减
            $result->filter(function ($memberCoupon) use($coupon_method){
                return $memberCoupon->belongsToCoupon->coupon_method == $coupon_method;
            });
        }
        //dd($result->toArray());exit;
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