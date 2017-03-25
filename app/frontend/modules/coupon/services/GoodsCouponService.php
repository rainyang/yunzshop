<?php
/**
 * Created by PhpStorm.
 * User: Rui
 * Date: 2017/3/24
 * Time: 18:24
 */

namespace app\frontend\modules\coupon\services;


class GoodsCouponService extends CouponService
{
    public function __construct($OrderModel, $memberCoupon)
    {
        parent::__construct($OrderModel, $memberCoupon);
        //exit('sdfs');
    }
}