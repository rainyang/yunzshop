<?php
/**
 * 订单详情
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/3/4
 * Time: 上午11:16
 */

namespace app\backend\modules\order\controllers;

use app\backend\modules\order\models\Order;
use app\common\components\BaseController;

class DetailController extends BaseController
{
    public function index(\Request $request)
    {
        $orderId = $request->query('id');
        $order = Order::getOrderDetailById($orderId);

        return view('order.detail', [
            'order'         => $order ? $order->toArray() : [],
            'var'           => \YunShop::app()->get(),
            'ops'           => 'order.ops'
        ])->render();
    }
}