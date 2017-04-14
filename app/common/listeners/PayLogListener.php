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
        $pay_type = config('app.pay_type');

        $params = $event->getPayRequestParams();
        $pay = $event->getPayObject();

        $pay_order_info = PayOrder::getPayOrderInfo($params['out_trade_no'])->first()->toArray();

        $pay->payRequestDataLog($pay_order_info['id'], $pay_order_info['out_order_no'],
            $pay_type[$pay_order_info['third_type']], json_encode($params));
    }
}