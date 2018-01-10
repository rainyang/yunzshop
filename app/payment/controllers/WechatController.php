<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/28
 * Time: 上午6:50
 */

namespace app\payment\controllers;

use app\common\helpers\Url;
use app\common\models\AccountWechats;
use app\common\models\Order;
use app\common\models\OrderPay;
use app\common\services\Pay;
use app\payment\PaymentController;
use EasyWeChat\Foundation\Application;
use app\common\models\OrderGoods;


class WechatController extends PaymentController
{
    private $pay_type = ['JSAPI' => '微信', 'APP' => '微信APP'];

    private $attach = [];

    public function __construct()
    {
        parent::__construct();

        if (empty(\YunShop::app()->uniacid)) {
            $post = $this->getResponseResult();
            if (\YunShop::request()->attach) {
                \Setting::$uniqueAccountId = \YunShop::app()->uniacid = \YunShop::request()->attach;
            } else {
                $this->attach = explode(':', $post['attach']);
                \Setting::$uniqueAccountId = \YunShop::app()->uniacid = $this->attach[0];
            }
            \Log::debug('---------attach数组--------', \YunShop::app()->uniacid);
            AccountWechats::setConfig(AccountWechats::getAccountByUniacid(\YunShop::app()->uniacid));
        }
    }

    public function notifyUrl()
    {
        $post = $this->getResponseResult();
        $this->log($post);

        $verify_result = $this->getSignResult($post);

        if ($verify_result) {
            $data = [
                'total_fee'    => $post['total_fee'] ,
                'out_trade_no' => $post['out_trade_no'],
                'trade_no'     => $post['transaction_id'],
                'unit'         => 'fen',
                'pay_type'     => $this->pay_type[$post['trade_type']],
                'pay_type_id'     => $post['trade_type'] == 'JSAPI' ? 1 : 9
            ];

            $this->payResutl($data);
            echo "success";
        } else {
            echo "fail";
        }
    }


    public function returnUrl()
    {
        if (\YunShop::request()->outtradeno) {
            $orderPay = OrderPay::where('pay_sn', \YunShop::request()->outtradeno)->first();
            $orders = Order::whereIn('id', $orderPay->order_ids)->get();
            if (is_null($orderPay)) {
                redirect(Url::absoluteApp('home'))->send();
            }

            if ($orders->count() > 1) {
                redirect(Url::absoluteApp('member/orderlist/', ['i' => \YunShop::app()->uniacid]))->send();
            } else {
                redirect(Url::absoluteApp('member/orderdetail/'.$orders->first()->id, ['i' => \YunShop::app()->uniacid]))->send();
            }
        } else {
            redirect(Url::absoluteApp('home'))->send();
        }
    }

    /**
     * 签名验证
     *
     * @return bool
     */
    public function getSignResult($post)
    {
        switch ($post['trade_type']) {
            case 'JSAPI':
                $pay = \Setting::get('shop.pay');

                if (isset($this->attach[1]) && $this->attach[1] == 'wechat') {
                    $min_set = \Setting::get('plugin.min_app');

                    $pay = [
                        'weixin_appid' => $min_set['key'],
                        'weixin_secret' => $min_set['secret'],
                        'weixin_mchid' => $min_set['mchid'],
                        'weixin_apisecret' => $min_set['api_secret'],
                        'weixin_cert'   => '',
                        'weixin_key'    => ''
                    ];
                }

                break;
            case 'APP' :
                $pay = \Setting::get('shop_app.pay');
                break;
        }

        $app = $this->getEasyWeChatApp($pay);
        $payment = $app->payment;
        $notify = $payment->getNotify();

        //老版本-无参数
        $valid = $notify->isValid();

        if (!$valid) {
            //新版本-有参数
            $valid = $notify->isValid($pay['weixin_apisecret']);
        }

        return $valid;
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
        //访问记录
        Pay::payAccessLog();
        //保存响应数据
        Pay::payResponseDataLog($post['out_trade_no'], '微信支付', json_encode($post));
    }
}