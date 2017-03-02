<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/3/1
 * Time: 下午10:31
 */

namespace app\frontend\modules\order\model\behavior;


class Order extends \app\common\models\Order
{
    public static function updateOrder($order_id, $data)
    {
        \app\common\models\Order::update($data)
            ->where('id', '=', $order_id)
            ->where('uniacid', '=', \YunShop::app()->uniacid);
    }

    public static function getDbOrder($order_id)
    {
        \app\common\models\Order::where('id', '=', $order_id)
            ->where('uniacid', '=', \YunShop::app()->uniacid)
            ->get();
    }
}