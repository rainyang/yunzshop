<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 24/03/2017
 * Time: 01:07
 */

namespace app\payment\controllers;

use app\backend\modules\refund\services\RefundOperationService;
use app\common\facades\Setting;
use app\common\helpers\Url;
use app\common\models\Order;
use app\common\models\PayOrder;
use app\common\models\PayRefundOrder;
use app\common\models\PayWithdrawOrder;
use app\common\models\refund\RefundApply;
use app\common\services\finance\Withdraw;
use app\common\services\Pay;
use app\payment\PaymentController;

class AlipayController extends PaymentController
{
    public function notifyUrl()
    {
        $this->log($_POST, '支付宝支付');

        $verify_result = $this->getSignResult();

        \Log::debug(sprintf('支付回调验证结果[%d]', intval($verify_result)));

        if ($verify_result) {
            if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
                $data = [
                    'total_fee' => $_POST['total_fee'],
                    'out_trade_no' => $_POST['out_trade_no'],
                    'trade_no' => $_POST['trade_no'],
                    'unit' => 'yuan',
                    'pay_type' => '支付宝'
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

        if ($verify_result) {
            if ($_GET['trade_status'] == 'TRADE_SUCCESS') {
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

        $this->refundLog($_POST, '支付宝退款');

        $verify_result = $this->getSignResult();

        \Log::debug(sprintf('支付回调验证结果[%d]', intval($verify_result)));

        if ($verify_result) {
            if ($_POST['success_num'] >= 1) {
                $plits = explode('^', $_POST['result_details']);

                if ($plits[2] == 'SUCCESS') {
                    $data = [
                        'total_fee' => $plits[1],
                        'trade_no' => $plits[0],
                        'unit' => 'yuan',
                        'pay_type' => '支付宝'
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
        $this->withdrawLog($_POST, '支付宝提现');

        $verify_result = $this->getSignResult();

        \Log::debug(sprintf('支付回调验证结果[%d]', intval($verify_result)));

        if ($verify_result) {
            if ($_POST['success_details']) {
                $plits = explode('^', $_POST['success_details']);

                if ($plits[4] == 'S') {
                    $data = [
                        'total_fee' => $plits[3],
                        'trade_no' => $plits[0],
                        'unit' => 'yuan',
                        'pay_type' => '支付宝'
                    ];
                }
            } else {
                $plits = explode('^', $_POST['fail_details']);

                if ($plits[4] == 'F') {
                    $data = [
                        'total_fee' => $plits[3],
                        'trade_no' => $plits[0],
                        'unit' => 'yuan',
                        'pay_type' => '支付宝'
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
        \Log::debug(sprintf('Uniacid[%d]', \YunShop::app()->uniacid));
        $key = \Setting::get('alipay-web.key');
        \Log::debug(sprintf('$key %s', $key));
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
        Pay::payResponseDataLog($post['out_trade_no'], $desc, json_encode($post));
    }

    public function refundLog($post, $desc)
    {
        //访问记录
        Pay::payAccessLog();
        //保存响应数据
        Pay::payResponseDataLog(0, $desc, json_encode($post));
    }

    public function withdrawLog($post, $desc)
    {
        //访问记录
        Pay::payAccessLog();
        //保存响应数据
        Pay::payResponseDataLog($post['batch_no'], $desc, json_encode($post));
    }


    /**
     * 支付宝退款回调操作
     *
     * @param $data
     */
    public function refundResutl($data)
    {
        \Log::debug('退款操作', 'refund.succeeded');

        $pay_order = PayOrder::getPayOrderInfoByTradeNo($data['trade_no'])->first();

        if (!$pay_order) {
            return \Log::error('未找到退款订单支付信息', $data);
        }
        $pay_refund_model = PayRefundOrder::getOrderInfo($pay_order->out_order_no);

        if (!$pay_refund_model) {
            return \Log::error('退款订单支付信息保存失败', $data);
        }

        $pay_refund_model->status = 2;
        $pay_refund_model->trade_no = $pay_refund_model->trade_no;
        $pay_refund_model->type = $data['pay_type'];
        $pay_refund_model->save();

        $refundApply = RefundApply::where('alipay_batch_sn',$data['batch_no'])->first();

        if (!isset($refundApply)) {
            return \Log::error('订单退款信息不存在', $data);
        }
        if (!(bccomp($refundApply->price, $data['total_fee'], 2) == 0)) {
            return \Log::error("订单退款金额错误(订单金额:{$refundApply->price}|退款金额:{$data['total_fee']})|比较结果:" . bccomp($refundApply->price, $data['total_fee'], 2) . ")");
        }


        \Log::debug('订单退款(退款申请id:' . $refundApply->id . ',订单id:' . $refundApply->order_id . ')');
        RefundOperationService::refundComplete(['id' => $refundApply->id]);


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