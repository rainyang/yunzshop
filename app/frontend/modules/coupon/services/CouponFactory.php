<?php
/**
 * Created by PhpStorm.
 * User: Rui
 * Date: 2017/3/24
 * Time: 18:26
 */

namespace app\frontend\modules\coupon\services;

use app\common\models\Coupon;

class CouponFactory
{
    public function createCoupon($OrderModel, $memberCoupon)
    {
        switch ($memberCoupon->belongsToCoupon->use_type){
            case Coupon::COUPON_ALL_USE:
            case Coupon::COUPON_ORDER_USE:
                return new OrderCouponService($OrderModel, $memberCoupon);
                break;
            case Coupon::COUPON_GOODS_USE:
                return new GoodsCouponService($OrderModel, $memberCoupon);
                break;
            case Coupon::COUPON_CATEGORY_USE:
                return new CategoryCouponService($OrderModel, $memberCoupon);
                break;
            default :
                break;
        }
    }
}