<?php

namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;
use app\common\models\Address;
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
        dd(Address::whereIn('areaname', ['河北省','石家庄市' , '桥西区'])->pluck('id'));
    }

    public function store()
    {
        $order = Order::where('plugin_id', Store::PLUGIN_ID)->first();
        $paymentTypes = app('PaymentManager')->make('OrderPaymentManager')->getOrderPaymentTypes($order);
        dd($paymentTypes);
        exit;
    }

}