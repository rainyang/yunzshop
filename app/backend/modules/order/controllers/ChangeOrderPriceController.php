<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/18
 * Time: 上午10:00
 */

namespace app\backend\modules\order\controllers;

use app\backend\modules\order\models\Order;
use app\common\components\BaseController;
use app\frontend\modules\order\services\OrderService;

class ChangeOrderPriceController extends BaseController
{

    public function index()
    {
        $order_model = Order::find(\YunShop::request()->order_id);
        return view('order.change_price',[
            'order_goods_model' => $order_model->hasManyOrderGoods,
            'order_model'       => $order_model,
            'change_num'        => 1//改价次数
        ]);
    }
}