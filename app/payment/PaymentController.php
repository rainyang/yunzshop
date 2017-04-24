<?php

namespace  app\payment;

use app\backend\modules\member\models\MemberRelation;
use app\common\components\BaseController;
use app\common\models\Order;
use app\common\models\PayOrder;
use app\common\models\PayRefundOrder;
use app\frontend\modules\finance\services\BalanceService;
use app\frontend\modules\order\services\OrderService;
use app\backend\modules\refund\services\RefundOperationService;

/**
 * Created by PhpStorm.
 * User: jan
 * Date: 24/03/2017
 * Time: 09:06
 */
class PaymentController extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        $this->init();
    }

    private function init()
    {
        $script_info = pathinfo($_SERVER['SCRIPT_NAME']);

        \Log::debug('执行脚本', $script_info['filename']);

        if (!empty($script_info)) {
            switch ($script_info['filename']) {
                case 'notifyUrl':
                    \YunShop::app()->uniacid = $this->getUniacid();
                    break;
                case 'refundNotifyUrl':
                case 'withdrawNotifyUrl':
                    $batch_no = !empty($_REQUEST['batch_no']) ? $_REQUEST['batch_no'] : '';

                    \Log::debug('支付宝订单批次号', $batch_no);

                    \YunShop::app()->uniacid = (int)substr($batch_no, 17, 5);

                    \Log::debug('当前公众号', \YunShop::app()->uniacid);
                    break;
                default:
                    \YunShop::app()->uniacid = $this->getUniacid();
                    break;
            }
        }

        \Setting::$uniqueAccountId = \YunShop::app()->uniacid;
    }

    private function getUniacid()
    {
        $body = !empty($_REQUEST['body']) ? $_REQUEST['body'] : '';
        $splits = explode(':', $body);

        if (!empty($splits[1])) {
            \Log::debug('当前公众号', intval($splits[1]));

            return intval($splits[1]);
        } else {
            return 0;
        }
    }

    /**
     * 支付回调操作
     *
     * @param $data
     */
    public function payResutl($data)
    {
        $type = $this->getPayType($data['out_trade_no']);
        $pay_order_model = PayOrder::getPayOrderInfo($data['out_trade_no'])->first();

        if ($pay_order_model) {
            $pay_order_model->status = 2;
            $pay_order_model->trade_no = $data['trade_no'];
            $pay_order_model->third_type = $data['pay_type'];
            $pay_order_model->save();
        }

        switch ($type) {
            case "charge.succeeded":
                \Log::debug('支付操作', 'charge.succeeded');

                $order_info = Order::where('uniacid',\YunShop::app()->uniacid)->where('order_sn', $data['out_trade_no'])->first();

                if ($data['unit'] == 'fen') {
                    $order_info->price = $order_info->price * 100;
                }

                if (bccomp($order_info->price, $data['total_fee'], 2) == 0) {
                    MemberRelation::checkOrderPay();

                    OrderService::orderPay(['order_id' => $order_info->id]);
                }
                break;
            case "recharge.succeeded":
                \Log::debug('支付操作', 'recharge.succeeded');

                (new BalanceService())->payResult([
                    'order_sn'=> $data['out_trade_no'],
                    'pay_sn'=> $data['trade_no']
                ]);
                break;
        }
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
                $pay_refund_model->trade_no = $data['trade_no'];
                $pay_refund_model->third_type = $data['pay_type'];
                $pay_refund_model->save();
            }
        }

        \Log::debug('退款操作', 'refund.succeeded');

        $order_info = Order::where('uniacid',\YunShop::app()->uniacid)->where('order_sn', $data['out_trade_no'])->first();

        $order_info->price = $order_info->price * 100;

        if (bccomp($order_info->price, $data['total_fee'], 2) == 0) {
            \Log::debug('订单事件触发');
            RefundOperationService::refundComplete(['order_id'=>$order_info->id]);
        }
    }
}