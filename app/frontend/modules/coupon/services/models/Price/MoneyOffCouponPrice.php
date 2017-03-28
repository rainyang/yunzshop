<?php
/**
 * 立减优惠券
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/25
 * Time: 下午5:21
 */

namespace app\frontend\modules\coupon\services\models\Price;

class MoneyOffCouponPrice extends CouponPrice
{

    public function getPrice()
    {
        return $this->dbCoupon->deduct;
    }

}