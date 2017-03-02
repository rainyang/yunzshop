<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/1
 * Time: 下午5:11
 */

namespace app\frontend\modules\order\controllers;

use app\common\models\Order;
use app\frontend\modules\order\service\OrderService;

class ListController
{
    public function index(){

        $param = [];
        $db_order_models = Order::waitPay()->with('hasManyOrderGoods')->get();
        //dd($db_order_models);
        $order_models = OrderService::getOrderModels($db_order_models);
        //$goods = $order_models[0]->orm_model->hasManyOrderGoods[0]->goods_price;
dd($order_models[0]->orm_model->status_name);
        exit;
    }
}