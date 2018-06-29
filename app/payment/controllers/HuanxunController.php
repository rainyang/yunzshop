<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/6/27
 * Time: 13:50
 */

namespace app\payment\controllers;


use app\common\helpers\Url;
use app\common\models\AccountWechats;
use app\common\services\Pay;
use app\payment\PaymentController;
use Yunshop\YunPay\services\YunPayNotifyService;

class HuanxunController extends PaymentController
{
    private $attach = [];

    public function __construct()
    {
        parent::__construct();

        if (empty(\YunShop::app()->uniacid)) {
            $this->attach = explode(':', $_POST['orderNo']);

            \Setting::$uniqueAccountId = \YunShop::app()->uniacid = $this->attach[1];

            if(empty($_REQUEST)) {
                return false;
            }

            $paymentResult = $_REQUEST['paymentResult'];
            $xmlResult = new \SimpleXMLElement($paymentResult);
            $uniacid = $xmlResult->GateWayRsp->body->Attach;

            \Setting::$uniqueAccountId = \YunShop::app()->uniacid = $uniacid;

            AccountWechats::setConfig(AccountWechats::getAccountByUniacid(\YunShop::app()->uniacid));
        }
    }

    public function notifyUrl()
    {
        $parameter = $_POST;

        $this->log($parameter);

        if(!empty($parameter)){
            if($this->getSignResult()) {
                if ($_POST['respCode'] == '0006') {
                    \Log::debug('------验证成功-----');
                    $data = [
                        'total_fee'    => floatval($parameter['transAmt']),
                        'out_trade_no' => $this->attach[0],
                        'trade_no'     => $parameter['transactionId'],
                        'unit'         => 'fen',
                        'pay_type'     => intval($_POST['productId']) == 112 ? '微信-YZ' : '支付宝-YZ',
                        'pay_type_id'     => intval($_POST['productId']) == 112 ? 12 : 15

                    ];

                    $this->payResutl($data);
                    \Log::debug('----结束----');
                    echo 'SUCCESS';
                } else {
                    //其他错误
                }
            } else {
                //签名验证失败
            }
        }else {
            echo 'FAIL';
        }
    }

    public function notifyQuickUrl()
    {
        $parameter = $_POST;
        \Log::debug('------notifyQuickUrl-----');
        $this->log($parameter);

        if(!empty($parameter)){
            $paymentResult = $parameter['paymentResult'];

            $xmlResult = new \SimpleXMLElement($paymentResult);
            $status   = $xmlResult->GateWayRsp->body->Status;
            $order_no = $xmlResult->GateWayRsp->body->MerBillNo;
            $amount   = $xmlResult->GateWayRsp->body->Amount;
            $trade_no   = $xmlResult->GateWayRsp->body->IpsBillNo;

            if($this->getSignResult()) {
                \Log::debug('------notify验证成功-----');
                if ($status == "Y") {
                    $data = [
                        'total_fee'    => floatval($amount),
                        'out_trade_no' => $order_no,
                        'trade_no'     => $trade_no,
                        'unit'         => 'yuan',
                        'pay_type'     => '环迅快捷支付',
                        'pay_type_id'     => 16

                    ];

                    $this->payResutl($data);
                    \Log::debug('----结束----');
                    echo 'SUCCESS';
                } elseif ($status == "N") {
                    $message = "交易失败";
                } else {
                    $message = "交易处理中";
                }
            } else {
                $message = "验证失败";
            }
        }else {
            echo 'FAIL';
        }
    }

    public function returnUrl()
    {
        $trade = \Setting::get('shop.trade');

        if (!is_null($trade) && isset($trade['redirect_url']) && !empty($trade['redirect_url'])) {
            return redirect($trade['redirect_url'])->send();
        }

        if (0 == $_GET['state'] && $_GET['errorDetail'] == '成功') {
            redirect(Url::absoluteApp('member/payYes', ['i' => $_GET['attach']]))->send();
        } else {
            redirect(Url::absoluteApp('member/payErr', ['i' => $_GET['attach']]))->send();
        }
    }

    public function returnQuickUrl()
    {
        $trade = \Setting::get('shop.trade');

        if(empty($_REQUEST)) {
            return false;
        }

        $paymentResult = $_REQUEST['paymentResult'];

        $xmlResult = new \SimpleXMLElement($paymentResult);
        $status = $xmlResult->GateWayRsp->body->Status;
        $uniacid = $xmlResult->GateWayRsp->body->Attach;
        $order_no =$xmlResult->GateWayRsp->body->MerBillNo;

        $url = Url::absoluteApp('member/payErr', ['i' => $uniacid]);

        if ($this->getSignResult()) { // 验证成功
            \Log::debug('-------验证成功-----');
            if ($status == "Y") {
                $url = Url::absoluteApp('member/payYes', ['i' => $uniacid]);

                if (!is_null($trade) && isset($trade['redirect_url']) && !empty($trade['redirect_url'])) {
                    $url  = $trade['redirect_url'];
                }

                $message = "交易成功";
            }elseif($status == "N")
            {
                $message = "交易失败";
            }else {
                $message = "交易处理中";
            }
        } else {
            $message = "验证失败";
        }

        \Log::debug("-----快捷支付{$order_no}----", [$message]);

        redirect($url)->send();
    }

    public function returnAccountUrl()
    {
        $url = yzAppFullUrl('member');
        redirect($url)->send();
    }

    public function frontUrl()
    {
        $trade = \Setting::get('shop.trade');

        if (!is_null($trade) && isset($trade['redirect_url']) && !empty($trade['redirect_url'])) {
            return redirect($trade['redirect_url'])->send();
        }

        if (0 == $_GET['state'] && $_GET['errorDetail'] == '成功') {
            redirect(Url::absoluteApp('member', ['i' => $_GET['attach']]))->send();
        } else {
            redirect(Url::absoluteApp('home', ['i' => $_GET['attach']]))->send();
        }
    }

    public function refundUrl()
    {
        $parameter = $_POST;

        if (!empty($parameter)) {
            if ($this->getSignResult()) {
                if ($_POST['respCode'] == '0000') {
                    //验证成功，业务逻辑
                } else {
                    //其他错误
                }
            } else {
                //签名验证失败
            }
        } else {
            echo 'FAIL';
        }
    }

    public function refundQuickUrl()
    {
        $parameter = $_POST;

        if (!empty($parameter)) {
            if ($this->getSignResult()) {
                if ($_POST['respCode'] == '0000') {
                    //验证成功，业务逻辑
                } else {
                    //其他错误
                }
            } else {
                //签名验证失败
            }
        } else {
            echo 'FAIL';
        }
    }

    /**
     * 签名验证
     *
     * @return bool
     */
    public function getSignResult()
    {
        $pay = \Setting::get('plugin.yun_pay_set');

        $notify = new YunPayNotifyService();
        $notify->setKey($pay['key']);
        $pay = \Setting::get('plugin.huanxun_set');

        $notify = app('Yunshop\Huanxun\services\HuanxunPayNotifyService');
        $notify->setKey($pay['key']);
        $notify->setCert($pay['cert']);
        $notify->setMerCode($pay['mchntid']);

        return $notify->verifySign();
    }

    /**
     * 支付日志
     *
     * @param $post
     */
    public function log($data)
    {
        $orderNo = explode(':', $data['orderNo']);
        //访问记录
        Pay::payAccessLog();
        //保存响应数据
        Pay::payResponseDataLog($orderNo[0], '芸微信支付', json_encode($data));
    }
}