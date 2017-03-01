<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/3/1
 * Time: 上午10:16
 */

namespace app\common\servicesModel;


class RefundAddress
{
    public static function getRefundAddress($data)
    {
        \app\common\models\RefundAddress::where($data)
            ->get();
    }
}