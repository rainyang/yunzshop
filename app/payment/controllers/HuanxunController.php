<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/6/27
 * Time: 13:50
 */

namespace app\payment\controllers;


use app\common\events\withdraw\WithdrawSuccessEvent;
use app\common\helpers\Url;
use app\common\models\AccountWechats;
use app\common\services\Pay;
use app\payment\PaymentController;
use Yunshop\YunPay\services\YunPayNotifyService;
use app\common\models\UniAccount;

class HuanxunController extends PaymentController
{
    private $attach = [];
    private $set = [];

    public function __construct()
    {
        parent::__construct();

        if (empty(\YunShop::app()->uniacid)) {
            $this->attach = explode(':', $_POST['orderNo']);

            \Setting::$uniqueAccountId = \YunShop::app()->uniacid = $this->attach[1];

            if(empty($_REQUEST)) {
                return false;
            }

            if ($_REQUEST['paymentResult']) {
                $paymentResult = $_REQUEST['paymentResult'];
                $xmlResult = new \SimpleXMLElement($paymentResult);
                $uniacid = $xmlResult->GateWayRsp->body->Attach;
            }

            if ($_REQUEST['ipsResponse']) {
                $xmlResult = simplexml_load_string($_REQUEST['ipsResponse'], 'SimpleXMLElement', LIBXML_NOCDATA);
                $uniAccount = UniAccount::get();
                foreach ($uniAccount as $u) {
                    \YunShop::app()->uniacid = $u->uniacid;
                    \Setting::$uniqueAccountId = $u->uniacid;
                    $set = \Setting::get('plugin.huanxun_set');
                    if ($set['mchntid'] == $xmlResult->argMerCode) {
                        $this->set = $set;
                    }
                }
                $ipsResult = $this->parseData($_REQUEST['ipsResponse']);
                $customerCode = str_replace($this->set['user_prefix'],'',$ipsResult['customerCode']);
                $uniacid = \Yunshop\Huanxun\frontend\models\AccountApply::getUniacidByCustomerCode($customerCode)['uniacid'];
            }

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
                        'pay_type'     => '电子钱包快捷支付',
                        'pay_type_id'     => 18

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

    public function notifyWithdrawalsUrl()
    {
        $parameter = $this->parseData($_REQUEST['ipsResponse']);
        \Log::debug('------notifyWithdrawalsUrl-----');
        $this->log($parameter);

        if(!empty($parameter)){
            if ($parameter['tradeState'] == 10) {
                \Log::debug('------环迅打款成功-----');
                event(new WithdrawSuccessEvent($parameter['merBillNo']));
                echo 'ipsCheckOk';
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

        $url = Url::shopSchemeUrl("?menu#/member/payErr?i={$uniacid}");

        if ($this->getSignResult()) { // 验证成功
            \Log::debug('-------验证成功-----');
            if ($status == "Y") {
                    $url = Url::shopSchemeUrl("?menu#/member/payYes?i={$uniacid}");

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

        \Log::debug("-----快捷支付{$uniacid}-{$order_no}----", [$message]);

        redirect($url)->send();
    }

    public function returnAccountUrl()
    {

        $url = Url::absoluteApp('member', ['i' => \YunShop::app()->uniacid]);
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

    //环迅回调信息验证
    protected function parseData($message)
    {
        $obj = simplexml_load_string($message, 'SimpleXMLElement', LIBXML_NOCDATA);
        $body = simplexml_load_string($this->decrypt($obj->p3DesXmlPara), 'SimpleXMLElement', LIBXML_NOCDATA);;
        return (array)$body->body;
    }

    /**
     * 数据解密
     * @param $encrypted
     * @return bool|string
     */
    public function decrypt($encrypted)
    {
        $set =
        $encrypted = base64_decode($encrypted);
        $key = str_pad($this->set['3des_key'], 24, '0');
        $td = mcrypt_module_open(MCRYPT_3DES, '', MCRYPT_MODE_CBC, '');
        $iv = $this->set['3des_vector'];
        $ks = mcrypt_enc_get_key_size($td);
        @mcrypt_generic_init($td, $key, $iv);
        $decrypted = mdecrypt_generic($td, $encrypted);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $y = $this->pkcs5_unpad($decrypted);
        return $y;
    }

    private function pkcs5_unpad($text)
    {
        $pad = ord($text{strlen($text) - 1});
        if ($pad > strlen($text)) {
            return false;
        }
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) {
            return false;
        }
        return substr($text, 0, -1 * $pad);
    }
}