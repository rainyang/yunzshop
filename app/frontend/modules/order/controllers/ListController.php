<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/1
 * Time: 下午5:11
 */

namespace app\frontend\modules\order\controllers;

use app\common\models\Order;

class ListController
{
    public function index(){
        $db_order_models = Order::waitPay()->with('hasManyOrderGoods')->get();
        //dd($db_order_models);
        $order_models = $db_order_models;
        dd($order_models[0]->button_models);
        exit;
    }
    public function waitPay(){
        $db_order_models = Order::waitPay()->with('hasManyOrderGoods')->get();
        //dd($db_order_models);
        $order_models = $db_order_models;
        dd($order_models[0]->button_models);
        exit;
    }
}