<?php

namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;


use app\frontend\modules\order\services\OrderService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yunshop\Recharge\models\OrderModel;
use Yunshop\StoreCashier\common\models\CashierGoods;


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
        dd(app('plugins'));
        //(new MessageService(\app\frontend\models\Order::completed()->first()))->received();
    }
    private function aliquot($a,$b){
        return $a/$b == (int)($a/$b);
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