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
        file_put_contents(storage_path('logs/Fixbug0611.txt'), print_r(date('Ymd His').'首单-订单创建'.PHP_EOL,1), FILE_APPEND);
        $order = Order::find($event->getOrderModel()->id);
        $shopOrderSet = Setting::get('shop.order');
        if (!$shopOrderSet['goods']) {
            file_put_contents(storage_path('logs/Fixbug0611.txt'), print_r(date('Ymd His').'首单-没有首单商品'.PHP_EOL,1), FILE_APPEND);
            return;
        }

        if ($order->is_plugin != 0 || $order->plugin_id != 0) {
            file_put_contents(storage_path('logs/Fixbug0611.txt'), print_r(date('Ymd His').'首单-不是商城订单'.PHP_EOL,1), FILE_APPEND);
            return;
        }

        $firstOrder = FirstOrder::select()
            ->where('uid', $order->uid)
            ->first();
        if ($firstOrder) {
            file_put_contents(storage_path('logs/Fixbug0611.txt'), print_r(date('Ymd His').'首单-存在首单'.PHP_EOL,1), FILE_APPEND);
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
            file_put_contents(storage_path('logs/Fixbug0611.txt'), print_r(date('Ymd His').'首单'.PHP_EOL,1), FILE_APPEND);
            FirstOrder::create([
                'order_id' => $order->id,
                'uid' => $order->uid,
                'shop_order_set' => $shopOrderSet['goods']
            ]);
        }
    }
}