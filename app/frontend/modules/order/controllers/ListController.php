<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/1
 * Time: 下午5:11
 */

namespace app\frontend\modules\order\controllers;


use app\frontend\modules\order\service\OrderService;

class ListController
{
    public function index(){
        $param = [];
        $order_models = OrderService::getOrderModels();
        var_dump($order_models);
    }
}