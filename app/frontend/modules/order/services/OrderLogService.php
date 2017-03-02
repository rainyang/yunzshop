<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/2/27
 * Time: 上午9:55
 */

namespace app\frontend\modules\services\order;

use app\common\models\Order;

class OrderLogService
{
    //验证log是否为空
    public static function verifyLogIsEmpty($log)
    {
        if (empty($log)) {
            return show_json(0, '支付出错,请重试!');
        }
    }

    //验证log 并返回plid
    public static function verifyLog($log, $uniacid, $openid, $ordersn_general, $price, $status)
    {
        if (!empty($log) && $log['status'] != '0') {
            return show_json(-1, '订单已支付, 无需重复支付!');
        }
        if (!empty($log) && $log['status'] == '0') {
            Order::deleteLog($log['plid']);
            $log = null;
        }
        $plid = $log['plid'];
        if (empty($log)) {
            $log = array(
                'uniacid' => $uniacid,
                'openid' => $openid,
                'module' => "sz_yi",
                'tid' => $ordersn_general,
                'fee' => $price,
                'status' => $status
            );
            $plid = Order::insertLog($log);
        }
        return $plid;
    }
}