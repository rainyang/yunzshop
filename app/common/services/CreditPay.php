<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2017/3/24
 * Time: 下午12:42
 */

namespace app\common\services;

use app\common\models\PayOrder;
use app\common\services\finance\Balance;

class CreditPay extends Pay
{
    public function __construct()
    {
    }

    public function doPay($params = [])
    {
        $data = [
            'member_id' => $params['member_id'],
            'change_money' => $params['amount'],
            'serial_number' => $params['order_no'],
            'operator' => $params['operator'],
            'operator_id' => $params['operator_id'],
            'remark' => $params['remark'],
            'service_type' => $params['service_type']
        ];

        $operation = '余额订单支付 订单号：' . $params['order_no'];
        $this->log($params['extra']['type'], '余额', $params['amount'], $operation,$params['order_no'], json_encode($params));

        self::payRequestDataLog($params['order_no'],$params['extra']['type'], '余额支付', json_encode($params));

        $result = (new Balance())->changeBalance($data);

        if ($result === true) {
            $pay_order_model = PayOrder::uniacid()->where('out_order_no', $params['order_no'])->first();

            if ($pay_order_model) {
                $pay_order_model->status = 2;
                $pay_order_model->trade_no = $data['trade_no'];
                $pay_order_model->third_type = $data['pay_type'];
                $pay_order_model->save();
            }

            return true;
        } else {
            return false;
        }


    }

    public function doRefund($out_trade_no, $totalmoney, $refundmoney)
    {
        // TODO: Implement doRefund() method.
    }

    public function doWithdraw($member_id, $out_trade_no, $money, $desc, $type)
    {
        // TODO: Implement doWithdraw() method.
    }

    public function buildRequestSign()
    {
        // TODO: Implement buildRequestSign() method.
    }
}