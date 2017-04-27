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
use app\common\models\PayWithdrawOrder;
use app\common\services\finance\Withdraw;
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
            if ($_POST['success_details']) {
                $plits = explode('^', $_POST['success_details']);

                if ($plits[4] == 'S') {
                    $data = [
                        'total_fee'    => $plits[3],
                        'trade_no'     => $_POST['batch_no'],
                        'unit'         => 'yuan',
                        'pay_type'     => '支付宝'
                    ];
                }
            } else {
                $plits = explode('^', $_POST['fail_details']);

                if ($plits[4] == 'F') {
                    $data = [
                        'total_fee'    => $plits[3],
                        'trade_no'     => $_POST['trade_no'],
                        'unit'         => 'yuan',
                        'pay_type'     => '支付宝'
                    ];
                }
            }

            $this->withdrawResutl($data);

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

    /**
     * 支付宝退款回调操作
     *
     * @param $data
     */
    public function refundResutl($data)
    {
        $pay_order = PayOrder::getPayOrderInfoByTradeNo($data['trade_no'])->first();

        if ($pay_order) {
            $pay_refund_model = PayRefundOrder::getOrderInfo($pay_order->out_order_no);

            if ($pay_refund_model) {
                $pay_refund_model->status = 2;
                $pay_refund_model->trade_no = $pay_refund_model->trade_no;
                $pay_refund_model->third_type = $data['pay_type'];
                $pay_refund_model->save();
            }
        }

        \Log::debug('退款操作', 'refund.succeeded');

        $order_info = Order::where('uniacid',\YunShop::app()->uniacid)->where('order_sn', $data['out_trade_no'])->first();

        if (bccomp($order_info->price, $data['total_fee'], 2) == 0) {
            \Log::debug('订单事件触发');
            RefundOperationService::refundComplete(['order_id'=>$order_info->id]);
        }
    }

    /**
     * 支付宝提现回调操作
     *
     * @param $data
     */
    public function withdrawResutl($data)
    {
        $pay_refund_model = PayWithdrawOrder::getOrderInfo($data['trade_no']);

        if ($pay_refund_model) {
            $pay_refund_model->status = 2;
            $pay_refund_model->trade_no = $data['trade_no'];
            $pay_refund_model->save();
        }

        \Log::debug('提现操作', 'withdraw.succeeded');

        if (bccomp($pay_refund_model->price, $data['total_fee'], 2) == 0) {
            Withdraw::paySuccess($data['trade_no']);
        }
    }
}