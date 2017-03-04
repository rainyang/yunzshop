<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/3
 * Time: 上午9:10
 */

namespace app\frontend\modules\order\controllers;
use app\common\models\Order;


class DetailController
{
    public function index(){
        $db_order_models = Order::with('hasManyOrderGoods')->first();
        $order = $db_order_models->toArray();
        dd($order);

        echo json_encode($db_order_models,JSON_UNESCAPED_UNICODE);
    }
}