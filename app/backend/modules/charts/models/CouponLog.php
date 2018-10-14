<?php
/**
 * Created by PhpStorm.
 * User: BC
 * Date: 2018/10/14
 * Time: 22:08
 */

namespace app\backend\modules\charts\models;


use app\common\models\MemberCoupon;

class CouponLog extends MemberCoupon
{
    /**
     * @param $searchTime
     * @return mixed
     */
    public function getGivenCount($searchTime)
    {
        if ($searchTime) {
            return self::uniacid()->whereBetween('created_at', [$searchTime['start'], $searchTime['end']])->sum('change_money');
        }
        return self::uniacid()->sum('change_money');
    }

    /**
     * @param $searchTime
     * @return mixed
     */
    public function getUsedCount($searchTime)
    {
        if ($searchTime) {
            return self::uniacid()->whereBetween('created_at', [$searchTime['start'], $searchTime['end']])->sum('change_money');
        }
        return self::uniacid()->sum('change_money');
    }
}