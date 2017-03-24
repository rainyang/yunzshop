<?php

/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2017/3/22
 * Time: 上午11:43
 */

/**
 * APP移动客户端支付宝支付功能
 */
namespace app\common\services\alipay;

use app\common\services\AliPay;

class MobileAlipay extends AliPay
{
    public function __construct()
    {}

    public function doPay($subject, $body, $amount, $order_no, $extra)
    {
        $alipay = app('alipay.mobile');
        $alipay->setOutTradeNo($order_no);
        $alipay->setTotalFee($amount);
        $alipay->setSubject($subject);
        $alipay->setBody($body);

        // 返回签名后的支付参数给支付宝移动端的SDK。
        echo $alipay->getPayPara();
    }
}