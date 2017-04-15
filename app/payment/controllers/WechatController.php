<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2017/3/28
 * Time: 上午6:50
 */

namespace app\payment\controllers;

use app\common\models\Order;
use app\payment\PaymentController;
use EasyWeChat\Foundation\Application;
use app\common\services\WechatPay;
use app\common\models\PayOrder;


class WechatController extends PaymentController
{
    public function notifyUrl()
    {
        file_put_contents(storage_path('logs/1.log'), 1);
        $post = $this->getResponseResult();

        if (config('app.debug')) {
            /*$post = Array
            (
                 'appid'  => 'wx6be17f352e859277',
                 'attach'  => 1,
                 'bank_type'  => 'CFT',
                 'cash_fee'  => 1,
                 'fee_type'  => 'CNY',
                 'is_subscribe'  => 'Y',
                 'mch_id'  => '1429240702',
                 'nonce_str'  => '58f1ee9b589d8',
                 'openid'  => 'oNnNJwqQwIWjAoYiYfdnfiPuFV9Y',
                 'out_trade_no'  => 'SN20170415175743883225',
                 'result_code'  => 'SUCCESS',
                 'return_code'  => 'SUCCESS',
                 'sign'  => '8B2EB0D7D9574B4516F5A4F61BB5474B',
                 'time_end'  => 20170415175807,
                 'total_fee'  => 1,
                 'trade_type'  => 'JSAPI',
                 'transaction_id'  => '4001322001201704157112987431'  //微信支付单号 可用于退款
            );*/


        //$post = $this->getResponseResult();
        if(isset($_GET['test_uid'])){
            $post = json_decode('{"trade_type":"JSAPI","body":"ss:2","out_trade_no":"SN20170415104105888074","total_fee":1,"nonce_str":"qvlNyG3N","device_info":"yun_shop","attach":1,"spbill_create_ip":"219.137.203.42","openid":"oNnNJwpdYZI0HNWQjnvZY99WEOpM"}',true);
        }
        \Log::debug(file_get_contents('php://input'));
        }

file_put_contents(storage_path('logs/2.log'), print_r($post, 1));
        //$this->log($post);

        $verify_result = $this->getSignResult();
        file_put_contents(storage_path('logs/3.log'), print_r($verify_result, 1));
        if (1) {

            file_put_contents(storage_path('logs/4.log'), 1);
            $data = [
                'total_fee'    => $post['total_fee'] ,
                'out_trade_no' => $post['out_trade_no'],
                'trade_no'     => $post['transaction_id']
            ];

            $this->payResutl($data);
            echo "success";
        } else {
            if(isset($_GET['test_uid'])) {
            }
            echo "fail";
        }
    }

    /**
     * 签名验证
     *
     * @return bool
     */
    public function getSignResult()
    {
        $pay = \Setting::get('shop.pay');

        $app = $this->getEasyWeChatApp($pay);
        $payment = $app->payment;

        $notify = $payment->getNotify();

        return $notify->isValid();
    }

    /**
     * 创建支付对象
     *
     * @param $pay
     * @return \EasyWeChat\Payment\Payment
     */
    public function getEasyWeChatApp($pay)
    {
        $options = [
            'app_id' => $pay['weixin_appid'],
            'secret' => $pay['weixin_secret'],
            // payment
            'payment' => [
                'merchant_id' => $pay['weixin_mchid'],
                'key' => $pay['weixin_apisecret'],
                'cert_path' => $pay['weixin_cert'],
                'key_path' => $pay['weixin_key']
            ]
        ];

        $app = new Application($options);

        return $app;
    }

    /**
     * 获取回调结果
     *
     * @return array|mixed|\stdClass
     */
    public function getResponseResult()
    {
        $input = file_get_contents('php://input');
        if (!empty($input) && empty($_POST['out_trade_no'])) {
            $obj = simplexml_load_string($input, 'SimpleXMLElement', LIBXML_NOCDATA);
            $data = json_decode(json_encode($obj), true);
            if (empty($data)) {
                exit('fail');
            }
            if ($data['result_code'] != 'SUCCESS' || $data['return_code'] != 'SUCCESS') {
                exit('fail');
            }
            $post = $data;
        } else {
            $post = $_POST;
        }

        return $post;
    }

    /**
     * 支付日志
     *
     * @param $post
     */
    public function log($post)
    {
        $pay = new WechatPay();

        //访问记录
        $pay->payAccessLog();
        //保存响应数据

        //$pay_order_info = PayOrder::getPayOrderInfo($post['out_trade_no'])->first()->toArray();
        //$pay->payResponseDataLog($pay_order_info['id'], $pay_order_info['out_order_no'], '微信支付', json_encode($post));
    }

    /**
     * 支付方式
     *
     * @param $order_id
     * @return string
     */
    public function getPayType($order_id)
    {
        if (!empty($order_id)) {
            $tag = substr($order_id, 0, 2);

            if ('SN' == strtoupper($tag)) {
                return 'charge.succeeded';
            } elseif ('RV' == strtoupper($tag)) {
                return 'recharge.succeeded';
            }
        }

        return '';
    }
}