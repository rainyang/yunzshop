<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/1
 * Time: 下午5:11
 */

namespace app\frontend\modules\order\controllers;
use app\common\components\BaseController;

use app\common\helpers\PaginationHelper;
use app\common\models\Order;

class ListController extends BaseController
{
    public function index(){
        $pageSize=5;
        $list = Order::waitPay()->with('hasManyOrderGoods')->paginate($pageSize);
        //dd($db_order_models);
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);

        $this->render('order/list', [
            'list' => $list,
            'pager' => $pager,
        ]);

    }
    public function waitPay(){
        $db_order_models = Order::waitPay()->with('hasManyOrderGoods')->get();
        //dd($db_order_models);
        $order_models = $db_order_models;
        dd($order_models[0]->button_models);
        exit;
    }
}