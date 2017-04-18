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
    public function index(\Request $request)
    {
        $orderId = $request->query('id');
        //$order_id = 1;
        $order = Order::with(
            [
                'hasManyOrderGoods.belongsToGood',
                'beLongsToMember',
                'hasOneOrderRemark',
                'address'
            ]
        )->find($orderId);
        $order->button_models = $order->button_models;
        $order = $order->toArray();
//        dd($order);
        //exit;
        return view('order.detail', [
            'order' => $order,
            'lang' => $this->_lang(),
            'totals'=> $this->_totals(),
            'dispatch' => ['id' => 1],
            'var'      => \YunShop::app()->get()
        ])->render();
    }

    private function _lang()
    {
        return array(
            'goods' => '商品',
            'good' => '商品',
            'orderlist' => '订单列表'
        );
    }

    private function _totals()
    {
        return array(
            'index' => '30',
            'waitPay' => '3',
            'waitSend' => '2',
            'waitReceive' => '5',
            'complete' => '6',
            'close' => '7',
            'waitRefund' => '2',
            'refund' => '1',
            'applyWithdraw' => '4',
        );
    }
}