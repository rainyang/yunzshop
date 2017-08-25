<?php

namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;


use app\common\exceptions\ShopException;
use app\common\models\order\OrderCoupon;
use app\common\models\order\OrderDeduction;
use app\frontend\models\Member;
use app\frontend\modules\order\services\OrderService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yunshop\Recharge\models\OrderModel;
use Yunshop\StoreCashier\common\models\CashierGoods;
use Yunshop\StoreCashier\common\models\CashierOrder;
use Yunshop\StoreCashier\common\models\Store;
use Yunshop\StoreCashier\common\models\StoreOrder;
use Yunshop\StoreCashier\frontend\Order\Models\Order;


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
        $r = Member::limit(5)->offset(1)->get();
        dd($r);
        exit;

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