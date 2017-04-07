<?php

namespace app\backend\modules\order\controllers;

use app\common\components\BaseController;
use app\common\models\Order;
use app\common\models\OrderGoods;
use app\common\services\TestContract;

/**
 * Created by PhpStorm.
 * User: jan
 * Date: 21/02/2017
 * Time: 11:34
 */
class FixController extends BaseController
{

    public function time()
    {
        Order::whereIn('status', [0, 1, 2, 3])->where('create_time', 0)->update(['create_time' => time()]);
        Order::whereIn('status', [1, 2, 3])->where('pay_time', 0)->update(['pay_time' => time()]);
        Order::whereIn('status', [2, 3])->where('send_time', 0)->update(['send_time' => time()]);
        Order::whereIn('status', [3])->where('finish_time', 0)->update(['finish_time' => time()]);
        Order::where('status', '-1')->where('cancel_time', 0)->update(['cancel_time' => time()]);
        echo 'ok';

    }

    public function deleteInvalidOrders()
    {
        Order::doesntHave('hasManyOrderGoods')->delete();
        Order::where('goods_price','<=',0)->delete();
        OrderGoods::where('goods_price','<=',0)->delete();
        echo 'ok';

    }

    public function payType()
    {
        Order::whereIn('status', [1, 2, 3])->where('pay_type_id', 0)->update(['pay_type_id' => 1]);
        echo 'ok';

    }
    public function dispatchType(){
        Order::whereIn('status', [2, 3])->where('dispatch_type_id', 0)->update(['dispatch_type_id' => 1]);
        echo 'ok';

    }
    public function index()
    {
        $this->time();
        $this->deleteInvalidOrders();
        $this->payType();
        $this->dispatchType();

    }
}