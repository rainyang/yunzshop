<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/1
 * Time: 下午5:11
 */

namespace app\frontend\modules\order\controllers;

use app\common\models\Goods;
use app\common\models\Order;
use app\frontend\modules\order\service\OrderService;
use Illuminate\Support\Facades\DB;

class ListController
{
    public function index(){
        DB::listen(function($sql) {
            var_dump($sql);
        });
        $param = [];
        $db_order_models = Order::waitPay()->get();
        Goods::first();
        //dd($db_order_models);
        $order_models = OrderService::getOrderModels($db_order_models);
        //$order_models[0]->orm_model->hasManyOrderGoods();

        exit;
    }
}