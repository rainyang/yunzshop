<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2017/3/24
 * Time: 下午12:42
 */

namespace app\common\services;

use app\common\models\Member;

class CreditPay extends Pay
{
    public function __construct()
    {
    }

    public function doPay($data = [])
    {
        //pay.php 980
        //支付单
        //订单 支付类型
        Member::setCredit($data['member_id'], $data['type'], $data['amount']);
    }

    public function doRefund($out_trade_no, $out_refund_no, $totalmoney, $refundmoney)
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