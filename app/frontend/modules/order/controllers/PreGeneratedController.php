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
use app\frontend\modules\order\services\OrderService;

abstract class PreGeneratedController extends ApiController
{

    protected function index()
    {

        $order_data = $this->getOrderData();
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
    protected function getOrderData(){
        $order_data = collect();
        $order_data->push(OrderService::getOrderData($this->getShopOrder()));

        $order_data = $order_data->merge($this->getPluginOrderData()[0]);
        return $order_data;
    }
    abstract protected function getMemberCarts();
    private function getShopOrder()
    {
        return OrderService::createOrderByMemberCarts($this->getMemberCarts());
    }

    private function getPluginOrderData()
    {
        $event = new ShowPreGenerateOrder($this->getMemberCarts());
        event($event);
        return $event->getData();
    }


}