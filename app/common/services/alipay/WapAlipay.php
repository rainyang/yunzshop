<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
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

    public function doPay($data = [])
    {
        $type = \YunShop::request()->type;

        if ($type == 7) {
            $isnewalipay = \Setting::get('shop_app.pay.newalipay');
            if ($isnewalipay == 1) {
                \Log::info('-------test-------', print_r($data,true));
//                $alipay = app('alipay.wap2');
//
//                $alipay->setOutTradeNo($data['order_no']);
//                $alipay->setTotalFee($data['amount']);
//                $alipay->setSubject($data['subject']);
//                $alipay->setBody($data['body']);
//
//                // 跳转到支付页面。
//                return $alipay->getPayLink();
            }
        }

        // 创建支付单。
        $alipay = app('alipay.wap');

        $alipay->setOutTradeNo($data['order_no']);
        $alipay->setTotalFee($data['amount']);
        $alipay->setSubject($data['subject']);
        $alipay->setBody($data['body']);

        // 跳转到支付页面。
        return $alipay->getPayLink();
    }
}