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
    public function __construct()
    {
        parent::__construct();

        if (empty(\YunShop::app()->uniacid)) {
            $post = $this->getResponseResult();

            \Setting::$uniqueAccountId = \YunShop::app()->uniacid = $post['attach'];
        }
    }

    public function notifyUrl()
    {
        $post = $this->getResponseResult();

        //$this->log($post);

        $verify_result = $this->getSignResult();

        if ($verify_result) {
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