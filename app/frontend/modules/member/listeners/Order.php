<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/1
 * Time: 下午4:49
 */

namespace app\frontend\modules\member\listeners;

use app\common\events\order\AfterOrderCreatedImmediatelyEvent;

class Order
{
    public function handle(AfterOrderCreatedImmediatelyEvent $event){
        $order = $event->getOrder();
        $goods_ids = $order->orderGoods->pluck('goods_id');
        $goods_option_ids = $order->orderGoods->pluck('goods_option_id');

        //过滤空值
        $goods_option_ids = array_filter($goods_option_ids);

        app('OrderManager')->make('MemberCart')->uniacid()->whereIn('goods_id', $goods_ids)->delete();
        app('OrderManager')->make('MemberCart')->uniacid()->whereIn('option_id', $goods_option_ids)->delete();

    }
}