<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/3/14
 * Time: 上午10:09
 */

namespace app\frontend\modules\order\services;


use app\common\models\Order;
use app\frontend\modules\order\services\behavior\OrderClose;

class AutoCancel
{
    //自动关闭
    public static function autoCancel()
    {
        $orders = Order::waitPay()->get();
        if ($orders) {
            self::query($orders);
        }
    }

    private static function query($orders)
    {
        foreach ($orders as $order)
        {
            $close_class = new OrderClose($order);
            if ($close_class->closeable()) {
                $close_class->close();
            }
        }
    }
}