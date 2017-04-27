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
use Illuminate\Support\Collection;

abstract class PreGeneratedController extends ApiController
{

    protected function index()
    {

        $order_data = $this->getOrderData();
        $total_price = $order_data->sum('order.price');
        $total_goods_price = $order_data->sum('order.goods_price');
        $total_dispatch_price = $order_data->sum('order.dispatch_price');
        $total_discount_price = $order_data->sum('order.discount_price');
        // todo 下面的代码需要重构
        //将订单中的优惠券 合并摊平到数组外层
        $data['discount']['coupon'] = $order_data->map(function ($order_data) {
            return $order_data['discount']['coupon'];
        })->collapse();
        //将订单中的收获地址 拿到外层
        $data['dispatch'] = $order_data[0]['dispatch'];
        //删掉内层的数据
        $order_data->map(function ($order_data) {
            $order_data['discount']->forget('coupon');
            return $order_data->forget('dispatch');
        });

        $data += [
            'total_price' => sprintf('%.2f',$total_price),
            'total_dispatch_price' => sprintf('%.2f',$total_price),
            'order_data' => $order_data,
            'total_goods_price' => sprintf('%.2f',$total_goods_price),
            'total_discount_price' => sprintf('%.2f',$total_discount_price)
        ];
        //dd($data);
        //exit;
        return $this->successJson('成功', $data);

    }

    /**
     * 获取订单数据组
     * @return \Illuminate\Support\Collection|static
     */
    protected function getOrderData()
    {
        $order_data = collect();
        $shop_order = $this->getShopOrder();

        if (!empty($shop_order)) {
            $order_data->push(OrderService::getOrderData($shop_order));
        }

        $order_data = $order_data->merge($this->getPluginOrderData()[0]);

        return $order_data;
    }

    /**
     * 获取全部购物车记录
     * @return mixed
     */
    abstract protected function getMemberCarts();

    /**
     * 获取商城的订单
     * @return \app\frontend\modules\order\services\models\PreGeneratedOrderModel
     */
    protected function getShopOrder()
    {

        return OrderService::createOrderByMemberCarts($this->getShopMemberCarts());
    }

    /**
     * 获取商城的购物车组
     * @return Collection
     */
    protected function getShopMemberCarts()
    {
        return MemberCartService::filterShopMemberCart($this->getMemberCarts());
    }

    /**
     * 获取插件的订单数据
     * @return array
     */
    private function getPluginOrderData()
    {
        $event = new ShowPreGenerateOrder($this->getMemberCarts());
        event($event);
        return $event->getData();
    }


}