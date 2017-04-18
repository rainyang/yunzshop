<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2017/3/24
 * Time: 下午12:42
 */

namespace app\common\services;

use app\common\models\PayOrder;
use app\frontend\modules\finance\services\BalanceService;

class CreditPay extends Pay
{
    public function __construct()
    {
    }

    public function doPay($params = [])
    {
        $operation = '余额订单支付 订单号：' . $params['order_no'];
        $this->log($params['extra']['type'], '余额', $params['amount'], $operation,$params['order_no'], Pay::ORDER_STATUS_NON);

        self::payRequestDataLog($params['order_no'],$params['extra']['type'], '余额', json_encode($params));

        $data = [
            'money' => $params['amount'],
            'serial_number' => $params['order_no'],
            'operator' => $params['operator'],
            'operator_id' => $params['operator_id'],
            'remark' => $params['remark'],
            'service_type' => $params['service_type']
        ];

        $result = (new BalanceService())->balanceChange($data);

        if ($result === true) {
            $pay_order_model = PayOrder::uniacid()->where('out_order_no', $params['order_no'])->first();

            if ($pay_order_model) {
                $pay_order_model->status = 2;
                $pay_order_model->trade_no = $params['trade_no'];
                $pay_order_model->third_type = '余额';
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