<?php

namespace app\payment\controllers;

use app\common\helpers\Url;
use app\common\models\AccountWechats;
use app\payment\PaymentController;
use app\frontend\modules\finance\models\BalanceRecharge;
use app\common\services\Pay;
use app\common\models\Order;
use app\common\models\OrderPay;

class EupController extends PaymentController
{

    //原始数据
    private $xml;


    private $parameter = [];

    public function __construct()
    {
        parent::__construct();

        if (empty(\YunShop::app()->uniacid)) {

            $this->xml = file_get_contents('php://input');

            $obj = simplexml_load_string($output, 'SimpleXMLElement', LIBXML_NOCDATA);

            $this->parameter = json_decode(json_encode($obj));

            \Setting::$uniqueAccountId = \YunShop::app()->uniacid = $this->parameter['attach'];

            AccountWechats::setConfig(AccountWechats::getAccountByUniacid(\YunShop::app()->uniacid));
        }
    }

    //异步充值通知
    public function notifyUrl()
    {
        \Log::debug('------------威富通微信异步通知----------------');
        $this->log($this->parameter);
        if($this->getSignResult()) {
            \Log::info('------威富通微信验证成功-----');
            if ($this->getParameter('status') == 0 && $this->getParameter('result_code') == 0) {
                $orderPay = OrderPay::where('pay_sn', $this->getParameter('out_trade_no'))->first();
                $orders = Order::whereIn('id', $orderPay->order_ids)->get();
                if (!$orders->isEmpty()) {
                    \Log::info('-------威富通微信支付开始----------');
                    if ($orderPay->status != 1) {
                        $data = [
                            'total_fee'    => floatval($this->getParameter('total_fee')),
                            'out_trade_no' => $this->getParameter('out_trade_no'),
                            'trade_no'     => 'wft_pay',
                            'unit'         => 'fen',
                            'pay_type'     => '威富通微信支付',
                            'pay_type_id'  => 20,
                        ];
                        $this->payResutl($data);
                    }
                    \Log::info('---------威富通微信支付结束-------');
                    echo 'success';
                    exit();
                } else {
                    //订单不存在
                    echo 'failure';
                    exit();
                }
            } else {
                //支付失败
                echo 'failure';
                exit();
            }
        } else {
            //签名验证失败
            echo 'failure';
            exit();
        }
    }

    //同步充值通知
    public function returnUrl()
    {
    }


    /**
     * 签名验证
     *
     * @return bool
     */
    public function getSignResult()
    {
        $swiftpassSign = strtolower($this->getParameter('sign'));
        $md5Sign = $this->getMD5Sign();

        return $swiftpassSign == $md5Sign;
    }

    //MD5签名
    public function getMD5Sign() {
        $signPars = "";
        ksort($this->parameters);
        foreach($this->parameters as $k => $v) {
            if("sign" != $k && "" != $v) {
                $signPars .= $k . "=" . $v . "&";
            }
        }
        $signPars .= "key=" . $this->getKey();

        return strtolower(md5($signPars));
    }

    /**
     * @param 获取密钥
     * @return mixed
     */
    public function getKey()
    {
        $set = \Setting::get('plugin.wft_pay');

        return $set['key'] ?:'';
    }

    /**
     *获取参数值
     */
    public function getParameter($parameter) {
        return isset($this->parameters[$parameter])?$this->parameters[$parameter] : '';
    }

    /**
     * 支付日志
     *
     * @param $post
     */
    public function log($data)
    {
        //访问记录
        Pay::payAccessLog();
        //保存响应数据
        Pay::payResponseDataLog($this->getParameter('out_trade_no'), '威富通支付', json_encode($data));
    }
}