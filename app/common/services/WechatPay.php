<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2017/3/17
 * Time: 下午12:00
 */

namespace app\common\services;

use app\common\models\MemberShopInfo;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Payment\Order;

class WechatPay extends Pay
{
    function __construct()
    {
        parent::__construct();

        //$this->GatewaySubmit();
    }

    /**
     * init
     *
     * @return void
     */
    function GatewaySubmit() {
        $this->gateUrl = "https://api.mch.weixin.qq.com/pay/unifiedorder";
        $this->key = "";
        $this->parameters = array();
    }


    public function doPay($subject, $body, $amount, $order_no, $extra)
    {
        $this->payAccessLog();
        $this->payLog();

        $user_info = MemberShopInfo::getMemberShopInfo(\YunShop::app()->getMemberId());

        $pay = Setting::get('shop.pay');
/*
        $this->setParameter('appid', $pay['appid']);
        $this->setParameter('mch_id', $pay['mch_id']);
        $this->setParameter('nonce_str', random(8) . "");
        $this->setParameter('body', $body);
        $this->setParameter('device_info', 'sz_yi');
        $this->setParameter('attach', $extra);
        $this->setParameter('out_trade_no', $order_no);
        $this->setParameter('total_fee', $amount * 100);
        $this->setParameter('spbill_create_ip', $this->getClientIP());
        $this->setParameter('notify_url', \YunShop::app()->siteroot. "addons/sz_yi/payment/wechat/notify.php");
        $this->setParameter('trade_type', 'JSAPI');
        $this->setParameter('openid', $user_info['openid']);

        $this->buildRequestSign();
        $response = $this->preOrder();
*/

        $options = [
            'app_id'  => $pay['weixin_appid'],
            'secret'  => $pay['weixin_secret'],
            'token'   => \YunShop::app()->account['token'],
            'aes_key' => \YunShop::app()->account['encodingaeskey'],
            // payment
            'payment' => [
                'merchant_id'        => $pay['weixin_mchid'],
                'key'                => $pay['weixin_apisecret'],
                'cert_path'          => $pay['weixin_cert'],
                'key_path'           => $pay['weixin_key'],
                'notify_url'         => \YunShop::app()->siteroot. "addons/sz_yi/payment/wechat/notify.php"
            ]
        ];

        $app = new Application($options);
        $payment = $app->payment;

        $attributes = [
            'trade_type'       => 'JSAPI', // JSAPI，NATIVE，APP...
            'body'             => $body,
            'out_trade_no'     => $order_no,
            'total_fee'        => $amount * 100, // 单位：分
            'nonce_str'        => random(8) . "",
            'device_info'      => 'sz_yi',
            'attach'           => $extra,
            'spbill_create_ip' => $this->getClientIP(),
            'openid'           => $user_info['openid']
        ];

        $order = new Order($attributes);

        $result = $payment->prepare($order);

        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
            return show_json(1, $result);
        } else {
            return show_json(0);
        }
    }

    public function doRefund()
    {
        // TODO: Implement doRefund() method.
    }

    public function doWithdraw()
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