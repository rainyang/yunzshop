<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2017/3/28
 * Time: 上午6:50
 */

namespace app\payment\controllers;

use app\payment\PaymentController;
use EasyWeChat\Foundation\Application;
use app\common\services\WechatPay;
use app\common\models\PayOrder;

class WechatController extends PaymentController
{
    public function notifyUrl()
    {
        $post = $this->getResponseResult();

        $this->log($post);

        $verify_result = $this->getSignResult();

        if ($verify_result) {
            $total_fee = $post['total_fee'];
            $pay_log = [];
            if (bccomp($pay_log['price'], $total_fee, 2) == 0) {
                // TODO 更新支付单状态
                // TODO 更新订单状态
            }
            echo "success";
        } else {
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
            'token' => \YunShop::app()->account['token'],
            'aes_key' => \YunShop::app()->account['encodingaeskey'],
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


    public function log($post)
    {
        $pay = new WechatPay();

        //访问记录
        $pay->payAccessLog();
        //保存响应数据
        $pay_order_info = PayOrder::getPayOrderInfo($post['out_trade_no'])->first()->toArray();
        $pay->payResponseDataLog($pay_order_info['id'], $pay_order_info['out_order_no'], '微信支付', json_encode($post));
    }
}