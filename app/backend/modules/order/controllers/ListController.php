<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/4
 * Time: 上午9:09
 */

namespace app\backend\modules\order\controllers;

use app\backend\modules\order\models\Order;
use app\common\components\BaseController;

use app\common\helpers\PaginationHelper;

class ListController extends BaseController
{
    public function index()
    {
        /*$params = [
            'search' => [
                'ambiguous' => [
                    'field' => 'order_goods',
                    'string' => '春',
                ],
                'pay_type' => 1,
                'time_range' => [
                    'field' => 'create_time',
                    'range' => [1458425047, 1498425047]
                ]
            ]
        ];*/
        $params = \YunShop::request();
        $pageSize = 2;

        $order_builder = Order::search($params['search']);

        $total_price = $order_builder->sum('price');

        $list = $order_builder->with([
            'belongsToMember' => $this->member_builder(),
            'hasManyOrderGoods' => $this->order_goods_builder(),
            'hasOneDispatchType',
            'hasOnePayType'
        ])->paginate($pageSize)->toArray();
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);
        //dd($list);
        $data = [
            'list' => $list,
            'total_price' => $total_price,
            'lang' => $this->_lang(),
            'totals' => $this->_totals(),
            'pager' => $pager,
        ];
        $data += $this->fakeData();
        $this->render('order/list', $data);

    }

    public function test()
    {
        $params = [
            'search' => [
                'base_info' => 1,
                'member_info' => 1,
                'goods_info' => 1,
                'pay_type' => 1,
                'time_fields' => 'create_time',
                'time_range' => [0, 0]
            ]
        ];
        $pageSize = 2;

        $order_builder = Order::searchByTime($params['search']['time_fields'], $params['search']['time_range'])
            ->searchLike($params['search']['base_info']);

        $total_price = $order_builder->sum('price');

        $list = $order_builder->with([
            'belongsToMember' => $this->member_builder($params['search']['member_info']),
            'hasManyOrderGoods' => $this->order_goods_builder($params['search']['goods_info'])
        ])->paginate($pageSize)->toArray();
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);
        dd($list);
    }

    private function member_builder()
    {
        return function ($query) {
            return $query->select(['uid', 'mobile', 'nickname', 'realname']);
        };
    }

    private function order_goods_builder()
    {
        return function ($query) {
            $query->select(['id', 'order_id', 'goods_id', 'goods_price', 'total', 'price', 'thumb', 'title', 'goods_sn']);
        };
    }

    public function waitPay()
    {
        $db_order_models = Order::waitPay()->with('hasManyOrderGoods')->get();
        //dd($db_order_models);
        $order_models = $db_order_models;
        dd($order_models[0]->button_models);
        exit;
    }

    private function fakeData()
    {
        return array(
            'supplierapply' => '',
            'stores' => '',
            'list' => '',
            'yunbiset' => '',
            'card_plugin' => '',
            'perm_role' => '',
            'sstarttime' => 0,
            'r_type' => '',
            'costmoney' => '',
            'card_set' => '',
            'liveRooms' => '',
            'paytype' => '',
            'sendtime' => 0,
            'cashier_stores' => '',
            'supplier' => '',
            'type' => '',
            'store' => '',
            'status' => '',
            'pendtime' => 0,
            'p_cashier' => '',
            'liveRoom' => '',
            'cashier_store' => '',
            'key' => '',
            'endtime' => 0,
            'fendtime' => 0,
            'shopset' => '',
            'pstarttime' => 0,
            'level' => '',
            'suppliers' => '',
            'request' => '',
            'fstarttime' => 0,
            'starttime' => 0,
            'agentid' => '',
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