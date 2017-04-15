<?php
namespace app\frontend\modules\coupon\services\models\TimeLimit;

use Carbon\Carbon;

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/29
 * Time: 下午5:14
 */
class DateTimeRange extends TimeLimit
{
    public function valid()
    {
        if($this->dbCoupon->time_start->greaterThan(Carbon::now())){
            //未开始
            return false;
        }

        if($this->dbCoupon->time_end->lessThan(Carbon::now())){
            //已结束
            return false;
        }
        return true;
    }
}