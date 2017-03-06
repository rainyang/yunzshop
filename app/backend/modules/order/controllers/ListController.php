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
        $pageSize = 2;
        $list = Order::with('belongsToMember','hasOneOrderDispatch','hasManyOrderGoods.hasOneGoods')->paginate($pageSize)->toArray();
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);
        //dd($list);
        $data = [
            'list' => $list['data'],
            'lang' => $this->_lang(),
            'totals'=> $this->_totals(),
            'pager' => $pager,
        ];
        $data+=$this->fakeData();
        $this->render('order/list', $data);

    }
    public function test(){
        $list = Order::with('hasOneOrderDispatch')->first();

        dd($list);


    }
    public function waitPay()
    {
        $db_order_models = Order::waitPay()->with('hasManyOrderGoods')->get();
        //dd($db_order_models);
        $order_models = $db_order_models;
        dd($order_models[0]->button_models);
        exit;
    }

    private function fakeData(){
        return array(
            'supplierapply'=>'',
            'stores'=>'',
            'list'=>'',
            'yunbiset'=>'',
            'card_plugin'=>'',
            'perm_role'=>'',
            'sstarttime'=>0,
            'r_type'=>'',
            'costmoney'=>'',
            'card_set'=>'',
            'liveRooms'=>'',
            'paytype'=>'',
            'sendtime'=>0,
            'cashier_stores'=>'',
            'supplier'=>'',
            'type'=>'',
            'store'=>'',
            'status'=>'',
            'pendtime'=>0,
            'p_cashier'=>'',
            'liveRoom'=>'',
            'cashier_store'=>'',
            'key'=>'',
            'endtime'=>0,
            'fendtime'=>0,
            'shopset'=>'',
            'pstarttime'=>0,
            'level'=>'',
            'suppliers'=>'',
            'request'=>'',
            'fstarttime'=>0,
            'starttime'=>0,
            'agentid'=>'',
        );
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