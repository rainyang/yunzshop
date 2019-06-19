<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019-06-09
 * Time: 17:22
 */

namespace app\common\listeners\order;


use app\common\events\order\AfterOrderCanceledEvent;
use app\common\events\order\AfterOrderPaidEvent;
use app\common\models\Order;
use app\common\facades\Setting;
use app\common\models\order\FirstOrder;

class FirstOrderListener
{
    public function handle(AfterOrderPaidEvent $event)
    {
        $order = Order::find($event->getOrderModel()->id);
        $shopOrderSet = Setting::get('shop.order');
        if (!$shopOrderSet['goods']) {
            return;
        }

        if ($order->is_plugin != 0 || $order->plugin_id != 0) {
            return;
        }

        foreach ($order->hasManyOrderGoods as $orderGoods) {
            if ($shopOrderSet['goods'][$orderGoods->goods_id]) {

                $firstOrder = FirstOrder::select()
                    ->where('uid', $order->uid)
                    ->where('goods_id', $orderGoods->goods_id)
                    ->first();
                if ($firstOrder) {
                    continue;
                }
                FirstOrder::create([
                    'order_id' => $order->id,
                    'goods_id' => $orderGoods->goods_id,
                    'uid' => $order->uid,
                    'shop_order_set' => $shopOrderSet['goods']
                ]);
            }
        }
    }

    public function cancel(AfterOrderCanceledEvent $event)
    {
        $order = Order::find($event->getOrderModel()->id);
        $ret = FirstOrder::select()
            ->where('order_id', $order->id)
            ->first();
        if ($ret) {
            $ret->delete();
        }
    }
}