<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2017/3/23
 * Time: 上午11:03
 */

/**
 * 手机WAP端支付宝支付功能
 */
namespace app\common\services\alipay;

use app\common\services\AliPay;

class WapAlipay extends AliPay
{
    public function __construct()
    {
        //todo
    }

    public function doPay($subject, $body, $amount, $order_no, $extra)
    {
        // 创建支付单。
        $alipay = app('alipay.wap');

        $alipay->setOutTradeNo($order_no);
        $alipay->setTotalFee($amount);
        $alipay->setSubject($subject);
        $alipay->setBody($body);


        // 跳转到支付页面。
        return $alipay->getPayLink();
    }
}