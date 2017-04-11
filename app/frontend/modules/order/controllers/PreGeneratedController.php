<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/23
 * Time: 上午11:11
 */

namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;
use app\common\events\order\ShowPreGenerateOrder;
use app\frontend\modules\member\services\MemberCartService;
use app\frontend\modules\order\services\OrderService;
use Request;

class PreGeneratedController extends ApiController
{
    private $param;
    private $memberCarts;

    public function index()
    {

        $this->param['goods'] = [
            'goods_id' => Request::query('goods_id'),
            'total' => Request::query('total'),
            'option_id' => Request::query('option_id'),
        ];

        $this->memberCarts[] = MemberCartService::newMemberCart($this->param['goods']);

        $this->run();
    }

    public function cart()
    {
        if (!isset($_GET['cart_ids'])) {
            return $this->errorJson('请选择要结算的商品');
        }

        $this->run();
    }


    private function getShopOrder()
    {
        $memberCarts = OrderService::getShopMemberCarts();
        return OrderService::createOrderByMemberCarts($memberCarts);
    }

    private function getPluginOrderData()
    {
        $event = new ShowPreGenerateOrder();
        event($event);
        return $event->getData();
    }

    private function run()
    {
        $order_data = collect();
        $order_data->push(OrderService::getOrderData($this->getShopOrder()));

        $order_data = $order_data->merge($this->getPluginOrderData()[0]);

        $total_price = $order_data->sum('order.price');
        $total_goods_price = $order_data->sum('order.goods_price');
        $total_dispatch_price = $order_data->sum('order.dispatch_price');

        $data['dispatch'] = $order_data[0]['dispatch'];
        $order_data->map(function ($order_data)
        {
            return $order_data->forget('dispatch');
        });

        $data += compact('total_price', 'total_dispatch_price', 'order_data', 'total_goods_price');
        return $this->successJson('成功', $data);

    }


}