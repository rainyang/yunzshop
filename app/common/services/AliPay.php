<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2017/3/17
 * Time: 下午12:01
 */

namespace app\common\services;

use app\common\components\alipay\AlipayServiceProvider;

class AliPay extends Pay
{
    public function __construct()
    {

    }

    public function doPay($subject, $body, $amount, $order_no, $extra)
    {
        // TODO: Implement doPay() method.
    }

    public function doRefund($out_trade_no, $out_refund_no, $totalmoney, $refundmoney)
    {
        // TODO: Implement doRefund() method.
    }

    public function doWithdraw($member_id, $out_trade_no, $money, $desc, $type)
    {
        // TODO: Implement doWithdraw() method.
    }

    /**
     * 构造签名
     *
     * @var void
     */
    public function buildRequestSign() {
        $signOrigStr = "";
        ksort($this->parameters);

        foreach($this->parameters as $k => $v) {
            if("" != $v && "sign" != $k) {
                $signOrigStr .= $k . "=" . $v . "&";
            }
        }
        $signOrigStr .= "key=" . $this->getKey();

        $sign = strtoupper(md5($signOrigStr));


        $this->setParameter("sign", $sign);
    }
}