<?php

namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;
use app\common\models\OrderPay;
use app\common\services\password\PasswordService;
use app\frontend\modules\coin\InvalidVirtualCoin;
use app\frontend\modules\deduction\models\Deduction;
use app\frontend\modules\finance\models\PointCoin;
use Yunshop\Love\Common\Models\LoveCoin;

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
        dd(OrderPay::first()->order_ids);
    }

    public function index1()
    {
        // 最简单的单例
        $result = app()->share(function ($var) {
            return $var + 1;
        });
        dd($result(100));

        dd($result(3));
    }

}