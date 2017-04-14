<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 24/03/2017
 * Time: 01:07
 */

namespace app\payment\controllers;


use app\common\facades\Setting;
use app\payment\PaymentController;

class AlipayController extends PaymentController
{
    public function notifyUrl()
    {
        $this->log($_POST);

        $verify_result = $this->getSignResult();

        if($verify_result) {
            if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
                $data = [
                    'total_fee'    => $_POST['total_fee'],
                    'out_trade_no' => $_POST['out_trade_no'],
                    'trade_no'     => $_POST['trade_no']
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
                redirect(request()->getSchemeAndHttpHost() . '/#success')->send();
            } else {
                redirect(request()->getSchemeAndHttpHost() . '/#fail')->send();
            }
        } else {
            redirect(request()->getSchemeAndHttpHost() . '/#fail')->send();
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

    public function log($post)
    {
        $pay = new WechatPay();

        //访问记录
        $pay->payAccessLog();
        //保存响应数据
        $pay_order_info = PayOrder::getPayOrderInfo($post['out_trade_no'])->first()->toArray();
        $pay->payResponseDataLog($pay_order_info['id'], $pay_order_info['out_order_no'], '支付宝支付', json_encode($post));
    }
}