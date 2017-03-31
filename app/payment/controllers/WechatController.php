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
use EasyWeChat\Payment\Notify;

class WechatController extends PaymentController
{
    public function notifyUrl()
    {
        // TODO 访问记录
        // TODO 保存响应数据

        $verify_result = $this->getSignResult();

        if($verify_result) {
            //商户订单号
            $out_trade_no = $_POST['out_trade_no'];
            //支付宝交易号
            $trade_no = $_POST['trade_no'];

            $total_fee = $_POST['total_fee'];

            if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
                // TODO 支付单查询 && 支付请求数据查询 验证请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的
                $pay_log = [];
                if (bccomp($pay_log['price'], $total_fee, 2) == 0) {
                    // TODO 更新支付单状态
                    // TODO 更新订单状态
                }
            }

            echo "success";

        } else {
            echo "fail";
        }
    }

    public function returnUrl()
    {
        // TODO 访问记录
        // TODO 保存响应数据

        $verify_result = $this->getSignResult();

        if($verify_result) {
            if($_GET['trade_status'] == 'TRADE_SUCCESS') {
                redirect()->send();
            }
        } else {
            echo "您提交的订单验证失败";
        }
    }

    public function refundNotifyUrl()
    {
        // TODO 访问记录
        // TODO 保存响应数据
    }

    public function withdrawNotifyUrl()
    {
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
        $notify = $app->getNotify();


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