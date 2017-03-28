<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2017/3/28
 * Time: 下午8:47
 */

namespace app\common\listeners;

use app\common\events\PayLog;
use app\common\models\PayOrder;

class PayLogListener
{
    public function handle(PayLog $event)
    {
        $params = $event->getPayRequestParams();
        $pay = $event->getPayObject();

        $pay_order_info = PayOrder::getPayOrderInfo($params['out_trade_no'])->first()->toArray();

        $pay->payRequestDataLog($pay_order_info['id'], $pay_order_info['type'],
            $pay_order_info['third_type'], json_encode($params));
    }
}