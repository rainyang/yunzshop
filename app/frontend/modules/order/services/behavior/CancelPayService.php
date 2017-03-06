<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/3/3
 * Time: 上午9:17
 */

namespace app\frontend\modules\order\services\behavior;
use app\common\models\Order;

class CancelPayService
{
    public static function refund($order)
    {
        if ($order->status != 1) {
            message("订单未付款，不需取消！");
        }
        //通过order_id 更改商品库存
        $order->status = 0;
        $order->cancelpay_time = time();
        $order->save();
        // log

        //message("取消订单付款操作成功！", order_list_backurl(), "success");
    }
}