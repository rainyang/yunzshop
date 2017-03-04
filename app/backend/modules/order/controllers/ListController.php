<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/4
 * Time: 上午9:09
 */

namespace app\backend\modules\order\controllers;

use app\common\components\BaseController;

use app\common\helpers\PaginationHelper;
use app\common\models\Order;

class ListController extends BaseController
{
    public function index()
    {
        $pageSize = 5;
        $list = Order::waitPay()->with('hasManyOrderGoods')->paginate($pageSize);
        //dd($db_order_models);
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);

        $this->render('order/list', [
            'order_list' => $list,
            'lang' => $this->_lang(),
            'totals'=> $this->_totals(),
            'pager' => $pager,
        ]);

    }

    public function waitPay()
    {
        $db_order_models = Order::waitPay()->with('hasManyOrderGoods')->get();
        //dd($db_order_models);
        $order_models = $db_order_models;
        dd($order_models[0]->button_models);
        exit;
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