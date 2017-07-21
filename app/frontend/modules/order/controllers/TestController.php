<?php

namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;

use app\common\events\order\AfterOrderCreatedEvent;
use app\common\events\order\AfterOrderPaidEvent;
use app\common\exceptions\AppException;
use app\common\models\AccountWechats;
use app\common\models\Member;
use app\common\models\Order;
use app\common\services\MessageService;
use app\frontend\modules\goods\services\GoodsService;
use app\frontend\modules\member\services\MemberService;

use app\frontend\modules\order\services\message\Message;
use app\frontend\modules\order\services\OrderManager;
use app\frontend\modules\order\services\OrderService;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Yunshop\Gold\common\services\Notice;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 21/02/2017
 * Time: 11:34
 */
class TestController extends ApiController
{
    public function index()
    {
        dd(\Setting::get('shop.trade.receive',10));
        dd(\Setting::get('shop.trade'));
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