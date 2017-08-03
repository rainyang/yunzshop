<?php

namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;


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
        dd(fmod(5.5,5.4));
        dd($this->aliquot(5.5,5.5));
        dd($this->aliquot(1.91,1.90));
        dd($this->aliquot(0.91,1.90));
        dd($this->aliquot(2.51,6.50));
        exit;
        dd(2.11/2.1);
        exit;
        dd(unserialize(CashierGoods::first()->plugins));
        dd(unserialize(CashierGoods::first()->profit));
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