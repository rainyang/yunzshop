<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/3/3
 * Time: 上午9:21
 */

namespace app\frontend\modules\order\services\behavior;

class CloseOrderService
{
    public static function refund($order)
    {
        if ($order->status == -1) {
            message("订单已关闭，无需重复关闭！");
        } else if ($order->status >= 1) {
            message("订单已付款，不能关闭！");
        }
        $order->status = -1;
        $order->cancel_time = time();
        //$order->remark = $order->remark . "【商家关闭原因】：" . \YunShop::request()->reson;
        $order->save();
        //dd($order);
        //退积分 写log
        //message("订单关闭操作成功！", order_list_backurl(), "success");
    }
}