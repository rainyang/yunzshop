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
    public function waitPay(){
        $db_order_models = Order::waitPay()->with('hasManyOrderGoods')->find(1);
        $order = $db_order_models->toArray();
    }
}