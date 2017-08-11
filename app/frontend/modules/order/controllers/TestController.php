<?php

namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;


use app\common\exceptions\ShopException;
use app\common\models\order\OrderCoupon;
use app\common\models\order\OrderDeduction;
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
        $store = Store::mine()->first();
        if (!isset($store)) {
            throw new ShopException('未找到所属店铺信息');
        }
        $store_id = $store['id'];
        $cashier_id = $store['cashier_id'];

        $orders_amount = Order::whereHas('storeOrder', function ($query) use ($store_id) {
            $query->where('store_id', $store_id);
        })->sum('price');
        $has_withdraw_amount = CashierOrder::where('cashier_id', $cashier_id)->where('has_withdraw',1)->sum('amount');
        $unWithdraw_amount = CashierOrder::where('cashier_id', $cashier_id)->where('has_withdraw',0)->sum('amount');

        // 抵扣
        $pointDeductionsAmount = OrderDeduction::whereDeductionId(1)->sum('amount');
        $couponAmount = OrderCoupon::sum('amount');

        compact('orders_amount','has_withdraw_amount','unWithdraw_amount','pointDeductionsAmount','couponAmount');

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