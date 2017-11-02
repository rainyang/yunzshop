<?php

namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;
use app\common\models\Order;
use app\frontend\modules\payment\orderPayments\BasePayment;
use Yunshop\StoreCashier\common\models\Store;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 21/02/2017
 * Time: 11:34
 */
class TestController extends ApiController
{
    public $transactionActions = [''];

    public function index()
    {
        $order = Order::where('id', 3310)->first();
        $paymentTypes = app('PaymentManager')->make('OrderPaymentManager')->getOrderPaymentTypes($order);
        $data = $paymentTypes->map(function (BasePayment $paymentType) {
            return [
                'name' => $paymentType->getName(),
                'value' => $paymentType->getCode(),
                'need_password' => $paymentType->needPassword(),
            ];
        });
        dd($data);
        exit;

        $result = [
            'name' => '支付宝',
            'value' => '2',
            'need_password' => '0'

        ];
        dd($paymentTypes);
        exit;
    }

    public function store()
    {
        $order = Order::where('plugin_id', Store::PLUGIN_ID)->first();
        $paymentTypes = app('PaymentManager')->make('OrderPaymentManager')->getOrderPaymentTypes($order);
        dd($paymentTypes);
        exit;
    }

}