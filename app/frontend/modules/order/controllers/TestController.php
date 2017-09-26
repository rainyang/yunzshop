<?php

namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;
use app\common\services\password\PasswordService;

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
        dd(\app\common\models\Order::find(3317)->hasOneDispatchType->needSend());

        //(new MessageService(\app\frontend\models\Order::completed()->first()))->received();
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