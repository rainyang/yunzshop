<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 24/03/2017
 * Time: 01:07
 */

namespace app\payment\controllers;


use app\payment\PaymentController;

class AlipayController extends PaymentController
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
        $verify_result = $this->getSignResult();

        if($verify_result) {
            file_put_contents('../../../../addons/sz_yi/data/p1.log', print_r(\YunShop::request(),1));
            exit;
            $success_details = $_POST['success_details']; //成功信息
            $batch_no = $_POST['batch_no']; //批次号
            $notify_time = $_POST['notify_time']; //通知时间
            if($success_details!=''){
                $success_details = explode('^',$success_details);
                $apply= array('status'=>'4','finshtime'=>strtotime($notify_time));
                pdo_update('sz_yi_commission_apply', $apply, array('batch_no' =>$batch_no));
            }

            //批量付款数据中转账失败的详细信息
            $fail_details = $_POST['fail_details']; //失败信息

            if($fail_details!=''){
                $fail_details = explode('^',$fail_details);
                if($fail_details['5']=='transfer_amount_not_enough'){
                    $fail_details['5']='账户余额不足';
                }
                $apply= array('status'=>'3','reason'=>$fail_details['5']);
                pdo_update('sz_yi_commission_apply', $apply, array('batch_no' =>$batch_no));
            }

            echo "success";
        }
        else {
            file_put_contents('../../../../addons/sz_yi/data/p2.log', 1);
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
        $alipay = app('alipay.web');

        return $alipay->verify();
    }
}