<?php
/**
 * Created by PhpStorm.
 * User: Rui
 * Date: 2017/3/24
 * Time: 18:26
 */

namespace app\frontend\modules\coupon\services;

class OrderCouponService extends CouponService
{
    public function __construct($OrderModel, $memberCoupon)
    {
        parent::__construct($OrderModel, $memberCoupon);
        //exit('sdfs');
    }
}