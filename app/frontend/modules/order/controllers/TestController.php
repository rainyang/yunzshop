<?php

namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;
use app\common\models\Order;
use app\common\models\OrderPay;
use app\common\services\password\PasswordService;
use app\frontend\modules\coin\InvalidVirtualCoin;
use app\frontend\modules\deduction\models\Deduction;
use app\frontend\modules\finance\models\PointCoin;
use Yunshop\Love\Common\Models\LoveCoin;
use Yunshop\SingleReturn\services\TimedTaskReturnService;
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
        $order = Order::where('id',3310)->first();
        $paymentTypes = app('PaymentManager')->make('OrderPaymentManager')->getOrderPaymentTypes($order);
        dd($paymentTypes);
        exit;
    }
    public function store(){
        $order = Order::where('plugin_id',Store::PLUGIN_ID)->first();
        $paymentTypes = app('PaymentManager')->make('OrderPaymentManager')->getOrderPaymentTypes($order);
        dd($paymentTypes);
        exit;
    }

}