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
        file_put_contents('../../../../addons/sz_yi/data/n.log', print_r($_POST,1));
        // TODO 访问记录
        // TODO 保存响应数据

        $verify_result = $this->getSignResult();

        if($verify_result) {
            file_put_contents('../../../../addons/sz_yi/data/s.log', print_r($_POST,1));
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
            file_put_contents('../../../../addons/sz_yi/data/k.log', print_r($_POST,1));
            echo "fail";
        }
    }

    public function returnUrl()
    {
        file_put_contents('../../../../addons/sz_yi/data/r.log', print_r($_GET,1));
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

    /**
     * 签名验证
     *
     * @return bool
     */
    public function getSignResult()
    {
        echo  'uniacid: '. \YunShop::app()->uniacid;
        $pay = Setting::get('shop.pay');
        echo '<pre> pay:';print_r($pay);exit;

        $alipay = app('alipay.web');
        $alipay->setSignType('MD5');
        $alipay->setKey();

        return $alipay->verify();
    }
}