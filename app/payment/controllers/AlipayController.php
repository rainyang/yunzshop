<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 24/03/2017
 * Time: 01:07
 */

namespace app\payment\controllers;


use app\common\facades\Setting;
use app\common\helpers\Url;
use app\common\services\AliPay;
use app\common\services\Pay;
use app\payment\PaymentController;

class AlipayController extends PaymentController
{
    public function notifyUrl()
    {
        $this->log($_POST, '支付宝支付');

        $verify_result = $this->getSignResult();

        \Log::debug('支付回调验证结果', intval($verify_result));

        if($verify_result) {
            if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
                $data = [
                    'total_fee'    => $_POST['total_fee'],
                    'out_trade_no' => $_POST['out_trade_no'],
                    'trade_no'     => $_POST['trade_no'],
                    'unit'         => 'yuan',
                    'pay_type'     => '支付宝'
                ];

                $this->payResutl($data);
            }

            echo "success";
        } else {
            echo "fail";
        }
    }

    public function returnUrl()
    {
        $verify_result = $this->getSignResult();

        if($verify_result) {
            if($_GET['trade_status'] == 'TRADE_SUCCESS') {
                redirect(Url::absoluteApp('member/payYes'))->send();
            } else {
                redirect(Url::absoluteApp('member/payErr'))->send();
            }
        } else {
            redirect(Url::absoluteApp('member/payErr'))->send();
        }
    }

    public function refundNotifyUrl()
    {
        \Log::debug('支付宝退款回调');

        $this->log($_POST, '支付宝退款');

        $verify_result = $this->getSignResult();

        \Log::debug('支付回调验证结果', intval($verify_result));

        if($verify_result) {
            if ($_POST['success_num'] >= 1) {
                $plits = explode('^', $_POST['result_details']);

                if ($plits[2] == 'SUCCESS') {
                    $data = [
                        'total_fee'    => $plits[1],
                        'trade_no'     => $plits[0],
                        'unit'         => 'yuan',
                        'pay_type'     => '支付宝'
                    ];

                    $this->refundResutl($data);
                }
            }

            echo "success";
        } else {
            echo "fail";
        }

    }

    public function withdrawNotifyUrl()
    {
        \Log::debug('支付宝提现回调');

        $this->log($_POST, '支付宝提现');

        $verify_result = $this->getSignResult();

        \Log::debug('支付回调验证结果', intval($verify_result));

        if($verify_result) {
            if ($_POST['success_num'] >= 1) {
                $plits = explode('^', $_POST['result_details']);

                if ($plits[2] == 'SUCCESS') {
                    $data = [
                        'total_fee'    => $plits[1],
                        'trade_no'     => $plits[0],
                        'unit'         => 'yuan',
                        'pay_type'     => '支付宝'
                    ];

                    $this->withdrawResutl($data);
                }
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
        $key = Setting::get('alipay-web.key');

        $alipay = app('alipay.web');
        $alipay->setSignType('MD5');
        $alipay->setKey($key);

        return $alipay->verify();
    }

    /**
     * 响应日志
     *
     * @param $post
     */
    public function log($post, $desc)
    {
        //访问记录
        Pay::payAccessLog();
        //保存响应数据
        Pay::payResponseDataLog($post['out_trade_no'], $desc , json_encode($post));
    }
}