<?php

namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;


use app\frontend\modules\goods\models\Brand;

use app\frontend\modules\order\services\OrderService;


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
        dd(\Setting::get('shop.notice.notice_enable.created'));
        exit;

        OrderService::autoClose();
        exit;
        // 这样下次 app()->make('OrderManager') 时, 会执行下面的闭包
        app('OrderManager')->extend('Order', function ($order, $app) {
            //例如 使实例出来的对象带有某些属性,记住容器类是一个创建型模式
            $order->uid = 1111;
            return $order;
        });
        dd(app('OrderManager')->make('Order'));
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