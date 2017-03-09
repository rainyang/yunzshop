<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/3/9
 * Time: 上午9:48
 */

namespace app\frontend\modules\order\services;


use app\common\models\CorePayLog;
use app\common\models\Order;

class VerifyPayService
{
    public static function verifyPay($order_id)
    {
        if (!$order_id) {
            return show_json(0, '参数错误!');
        }

        $db_order_model = Order::find($order_id);

        if (!$db_order_model) {
            return show_json(0, '订单未找到!');
        }
        if ($db_order_model->status == -1) {
            return show_json(-1, '订单已关闭, 无法付款!');
        } else if ($db_order_model->status >= 1) {
            return show_json(-1, '订单已付款, 无需重复支付!');
        }
    }

    public static function verifyLog(Order $order, $member)
    {
        $db_log_model = CorePayLog::select()->where('tid', '=', $order->hasOnePay->order_sn)->first();
        if ($db_log_model && $db_log_model->status != '0') {
            return show_json(-1, '订单已支付, 无需重复支付!');
        }
        if ($db_log_model && $db_log_model->status == '0') {
            $db_log_model->delete();
            $db_log_model = null;
        }
        if (!$db_log_model) {
            $log_data = [
                'uniacid'   => \YunShop::app()->uniacid,
                'member_id' => $member['id'],
                'tid'       => $order->hasOnePay->pay_sn,
                'fee'       => $order->price,
                'status'    => 0
            ];
            CorePayLog::create($log_data);
        }
    }
}