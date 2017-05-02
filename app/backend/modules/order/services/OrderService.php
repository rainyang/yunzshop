<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/5/2
 * Time: 下午4:28
 */

namespace app\backend\modules\order\services;


use app\backend\modules\order\models\Order;
use app\common\exceptions\AdminException;

class OrderService
{
    public static function close($order)
    {
        $order->status = Order::CLOSE;
        $order->save();
    }
}