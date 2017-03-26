<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2017/3/17
 * Time: 下午12:00
 */

namespace app\common\services;

use app\common\models\Member;
use app\common\models\MemberShopInfo;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Payment\Order as easyOrder;

class WechatPay extends Pay
{
    public function doPay($data = [])
    {
        //$this->payAccessLog();
        //$this->payLog(1, 1, $data['amount'], '微信订单支付 订单号：' . $data['order_no']);
        session()->put('member_id',123);

        $openid = Member::getOpenId(\YunShop::app()->getMemberId());
        $pay = \Setting::get('shop.pay');

        if (empty($pay['weixin_mchid']) || empty($pay['weixin_apisecret'])
            || empty($pay['weixin_appid']) || empty($pay['weixin_secret'])) {

            return error(1, '没有设定支付参数');
        }

        $app     = $this->getEasyWeChatApp($pay);
        $payment = $app->payment;
        $order = $this->getEasyWeChatOrder($data, $openid);
        $result = $payment->prepare($order);
        $prepayId = null;

        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
            $prepayId = $result->prepay_id;
        } else {
            return show_json(0);
        }

        $config = $payment->configForJSSDKPayment($prepayId);
        $config['appId'] = $pay['weixin_appid'];

        $js = $app->js;

        return ['config'=>$config, 'js'=>$js];
    }

    /**
     * 微信退款
     *
     * @param 订单号 $out_trade_no
     * @param 微信支付单号 $out_refund_no
     * @param 订单总金额 $totalmoney
     * @param 退款金额 $refundmoney
     * @return array
     */
    public function doRefund($out_trade_no, $out_refund_no, $totalmoney, $refundmoney)
    {
       // $this->payAccessLog();
        //$this->payLog(2, 1, $refundmoney, '微信退款 订单号：' . $out_trade_no . '退款单号：' . $out_refund_no . '退款总金额：' . $totalmoney);

        $pay = \Setting::get('shop.pay');

        if (empty($pay['weixin_mchid']) || empty($pay['weixin_apisecret'])) {
            return error(1, '没有设定支付参数');
        }

        if (empty($pay['weixin_cert']) || empty($pay['weixin_key']) || empty($pay['weixin_root'])) {
            message('未上传完整的微信支付证书，请到【系统设置】->【支付方式】中上传!', '', 'error');
        }

        $app     = $this->getEasyWeChatApp($pay);
        $payment = $app->payment;
        $result = $payment->refund($out_trade_no, $out_refund_no, $totalmoney*100, $refundmoney*100);

        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
            return show_json(1);
        } else {
            return show_json(0);
        }
    }


    public function doWithdraw($member_id, $out_trade_no, $money, $desc='', $type=1)
    {
        //$this->payAccessLog();
        //$this->payLog(3, 1, $money, '微信钱包提现 订单号：' . $out_trade_no . '提现金额：' . $money);

        $pay = \Setting::get('shop.pay');

        if (empty($pay['weixin_mchid']) || empty($pay['weixin_apisecret'])) {
            return error(1, '没有设定支付参数');
        }

        if (empty($pay['weixin_cert']) || empty($pay['weixin_key']) || empty($pay['weixin_root'])) {
            message('未上传完整的微信支付证书，请到【系统设置】->【支付方式】中上传!', '', 'error');
        }
/*
        $order_info = Order::getOrderInfoByMemberId($member_id)->first();

        if (!empty($order_info) && $order_info['status'] == 3) {
            $user_info = MemberShopInfo::getMemberShopInfo($order_info['member_id']);
        }*/

        $user_info = MemberShopInfo::getMemberShopInfo(123);

        $app = $this->getEasyWeChatApp($pay);

        if ($type == 1) {//钱包
            $merchantPay = $app->merchant_pay;

            $merchantPayData = [
                'partner_trade_no' => empty($out_trade_no) ? time() . random(4, true) : $out_trade_no,
                'openid' => $user_info['openid'],
                'check_name' => 'NO_CHECK',
                'amount' => $money * 100,
                'desc' => empty($desc) ? '佣金提现' : $desc,
                'spbill_create_ip' => $this->ip,
            ];

            $result = $merchantPay->send($merchantPayData);
        } else {//红包
            $luckyMoney = $app->lucky_money;

            $luckyMoneyData = [
                'mch_billno'       => $app['weixin_mchid'] . date('YmdHis') . rand(1000, 9999),
                'send_name'        => \YunShop::app()->account['name'],
                're_openid'        => $user_info['openid'],
                'total_num'        => 1,
                'total_amount'     => $money * 100,
                'wishing'          => empty($desc) ? '佣金提现红包' : $desc,
                'client_ip'        => $this->ip,
                'act_name'         => empty($act_name) ? '佣金提现红包' : $act_name,
                'remark'           => empty($remark) ? '佣金提现红包' : $remark,
                // ...
            ];
            $result = $luckyMoney->sendNormal($luckyMoneyData);
        }

        echo '<pre>';print_r($result);exit;
    }

    /**
     * 构造签名
     *
     * @var void
     */
    public function buildRequestSign()
    {
        // TODO: Implement buildRequestSign() method.
    }

    /**
     *
     * @param $pay
     * @return \EasyWeChat\Payment\Payment
     */
    public function getEasyWeChatApp($pay)
    {
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
                'notify_url'         => SZ_YI_WECHAT_NOTIFY_URL
            ]
        ];

        $app = new Application($options);

        return $app;
    }

    public function getEasyWeChatOrder($data, $openid)
    {
        $attributes = [
            'trade_type'       => 'JSAPI', // JSAPI，NATIVE，APP...
            'body'             => $data['body'],
            'out_trade_no'     => $data['order_no'],
            'total_fee'        => $data['amount'] * 100, // 单位：分
            'nonce_str'        => random(8) . "",
            'device_info'      => 'sz_yi',
            'attach'           => $data['extra'],
            'spbill_create_ip' => $this->ip,
            'openid'           => $openid
        ];

        return new easyOrder($attributes);
    }
}