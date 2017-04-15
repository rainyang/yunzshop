<?php

namespace  app\payment;

use app\backend\modules\member\models\MemberRelation;
use app\common\components\BaseController;
use app\common\models\Order;
use app\common\models\PayOrder;
use app\frontend\modules\order\services\OrderService;
use app\common\services\finance\Balance;

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

        $script_info = pathinfo($_SERVER['SCRIPT_NAME']);
        \Log::debug('入口' . print_r($_POST));
        echo '<pre>';print_r($script_info, 1);exit;
        file_put_contents(storage_path('logs/100.log'), print_r($_POST,1));
        if (!empty($script_info)) {
            switch ($script_info['filename']) {
                case 'notifyUrl':
                    file_put_contents(storage_path('logs/8.log'), $this->getUniacid());
                    \YunShop::app()->uniacid = $this->getUniacid();
                    break;
                case 'refundNotifyUrl':
                case 'withdrawNotifyUrl':
                    $batch_no = !empty($_REQUEST['batch_no']) ? $_REQUEST['batch_no'] : '';

                    \YunShop::app()->uniacid = substr($batch_no, 17, 5);
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
//        if (config('app.debug')) {
//            return 2;
//        }

        $body = !empty($_REQUEST['body']) ? $_REQUEST['body'] : '';
        $splits = explode(':', $body);

        if (!empty($splits[1])) {
            return intval($splits[1]);
        } else {
            return 0;
        }
    }

    public function payResutl($data)
    {
        $type = $this->getPayType($data['out_trade_no']);
        $pay_order_model = PayOrder::uniacid()->where('out_order_no', $data['out_trade_no'])->first();

        if ($pay_order_model) {
            $pay_order_model->status = 2;
            $pay_order_model->trade_no = $data['trade_no'];
            $pay_order_model->save();
        }
        file_put_contents(storage_path('logs/5.log'), 1);
        switch ($type) {
            case "charge.succeeded":
                $order_info = Order::uniacid()->where('order_sn', $data['out_trade_no'])->first();
                file_put_contents(storage_path('logs/6.log'), 1);
                if (bccomp($order_info->price, $data['total_fee'], 2) == 0) {
                    file_put_contents(storage_path('logs/7.log'), 1);
                    MemberRelation::checkOrderPay();

                    OrderService::orderPay(['order_id' => $data['out_trade_no']]);
                }
                break;
            case "recharge.succeeded":
                (new Balance())->payResult([
                    'order_sn'=> $data['out_trade_no'],
                    'pay_sn'=> $data['trade_no']
                ]);
                break;
        }
    }
}