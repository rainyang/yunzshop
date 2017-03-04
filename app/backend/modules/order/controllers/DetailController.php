<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/3/4
 * Time: 上午11:16
 */

namespace app\backend\modules\order\controllers;

use app\common\components\BaseController;
use app\common\models\Order;

class DetailController extends BaseController
{
    public function index()
    {
        $db_order_models = Order::waitPay()->with('hasManyOrderGoods')->first();
        $order = $db_order_models->toArray();
        $this->render('detail', [
            'order' => $order
        ]);
        dd($order);
    }
}