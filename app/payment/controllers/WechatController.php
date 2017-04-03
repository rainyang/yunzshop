<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2017/3/28
 * Time: 上午6:50
 */

namespace app\payment\controllers;

use app\common\helpers\Url;
use app\payment\PaymentController;
use EasyWeChat\Foundation\Application;

class WechatController extends PaymentController
{
    public function notifyUrl()
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

        file_put_contents(storage_path('logs/notify1.log'), print_r($post, 1));
        // TODO 访问记录
        // TODO 保存响应数据

        $verify_result = $this->getSignResult();

        if($verify_result) {
            file_put_contents(storage_path('logs/pp.log'), print_r($post,1));


//            $total_fee = $post['total_fee'];
//            $pay_log = [];
//            if (bccomp($pay_log['price'], $total_fee, 2) == 0) {
//                // TODO 更新支付单状态
//                // TODO 更新订单状态
//            }

            echo "success";

        } else {
           echo "fail";
        }
    }

    public function returnUrl()
    {
        // TODO 访问记录
        // TODO 保存响应数据

    }

    public function refundNotifyUrl()
    {
        file_put_contents(storage_path('logs/refund.log'), print_r($_POST, 1));
        // TODO 访问记录
        // TODO 保存响应数据
    }

    public function withdrawNotifyUrl()
    {
        file_put_contents(storage_path('logs/withdraw.log'), print_r($_POST, 1));
        // TODO 访问记录
        // TODO 保存响应数据
    }

    /**
     * 签名验证
     *
     * @return bool
     */
    public function getSignResult()
    {
        $pay = \Setting::get('shop.pay');

        $app     = $this->getEasyWeChatApp($pay);
        $payment = $app->payment;

        $notify  = $payment->getNotify();

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
                'notify_url'         => Url::shopUrl('payment/wechat/notifyUrl.php')
            ]
        ];

        $app = new Application($options);

        return $app;
    }
}