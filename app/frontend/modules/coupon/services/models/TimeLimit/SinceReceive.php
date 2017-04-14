<?php
namespace app\frontend\modules\coupon\services\models\TimeLimit;

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/29
 * Time: 下午5:17
 */
class SinceReceive extends TimeLimit
{
    public function valid()
    {
        if ($this->receiveDays() > $this->dbCoupon->time_days) {
            return false;
        }
        return true;
    }

    private function receiveDays()
    {
        return ceil((time() - $this->coupon->getMemberCoupon()->get_time) / 86400);
    }
}