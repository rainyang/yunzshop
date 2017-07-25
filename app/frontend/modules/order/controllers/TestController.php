<?php

namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;


use app\common\models\Order;
use app\frontend\modules\goods\models\Brand;

use app\frontend\modules\order\services\MessageService;
use app\frontend\modules\order\services\OrderService;


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

        (new MessageService(\app\frontend\models\Order::completed()->first()))->received();
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