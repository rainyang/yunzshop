<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/3/1
 * Time: 下午5:18
 */

namespace app\frontend\modules\order\services\model\behavior;


class OrderRefund extends \app\common\models\OrderRefund
{
    public static function getDbRefund($id)
    {
        return \app\common\models\OrderRefund::where('id', '=', $id)
            ->Where('status', '=', 0)
            ->orWhere('status', '>', 1)
            ->get();
    }

    public static function updateRefund($order_refund, $data)
    {
        \app\common\models\OrderRefund::update($data)
            ->where('id', '=', $order_refund['id']);
    }
}