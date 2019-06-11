<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019-06-09
 * Time: 17:22
 */

namespace app\common\listeners\order;


use app\common\events\order\AfterOrderCreatedEvent;
use app\common\models\Order;
use app\common\facades\Setting;
use app\common\models\order\FirstOrder;

class FirstOrderListener
{
    public function handle(AfterOrderCreatedEvent $event)
    {
        dd(11111);
        exit;
        $order = Order::find($event->getOrderModel()->id);
        $shopOrderSet = Setting::get('shop.order');
        if (!$shopOrderSet['goods']) {
            return;
        }

        if ($order->is_plugin != 0 || $order->plugin_id != 0) {
            return;
        }

        $firstOrder = FirstOrder::select()
            ->where('uid', $order->uid)
            ->first();
        if ($firstOrder) {
            return;
        }

        $firstOrderRet = false;
        foreach ($order->hasManyOrderGoods as $orderGoods) {
            if ($shopOrderSet['goods'][$orderGoods->goods_id]) {
                $firstOrderRet = true;
                break;
            }
        }
        if ($firstOrderRet) {
            FirstOrder::create([
                'order_id' => $order->id,
                'uid' => $order->uid,
                'shop_order_set' => $shopOrderSet['goods']
            ]);
        }
    }
}