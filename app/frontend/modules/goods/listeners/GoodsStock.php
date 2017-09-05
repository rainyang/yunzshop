<?php
namespace app\frontend\modules\goods\listeners;

use app\common\events\order\AfterOrderCreatedEvent;
use app\common\events\order\AfterOrderPaidEvent;
use app\common\models\OrderGoods;
use app\frontend\models\goods;
use app\frontend\models\GoodsOption;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/14
 * Time: 下午10:34
 */
class GoodsStock
{
    public function onOrderCreated(AfterOrderCreatedEvent $event){

        $order = $event->getOrderModel();
        $order->hasManyOrderGoods->map(function ($orderGoods){

            if($orderGoods->belongsToGood->reduce_stock_method != 0){
                return false;
            }
            $this->reduceStock($orderGoods);
        });
    }
    public function onOrderPaid(AfterOrderPaidEvent $event){

        $order = $event->getOrderModel();
        $order->hasManyOrderGoods->map(function ($orderGoods){
            $this->reduceStock($orderGoods);
        });
    }
    private function reduceStock($orderGoods){
        /**
         * @var $orderGoods OrderGoods
         */
        if($orderGoods->isOption()){
            $goodsOption = $orderGoods->goodsOption;
            /**
             * @var $goodsOption GoodsOption
             */
            $orderGoods->hasOneGoods->addSales($orderGoods->total);
            // 不是无限库存 减库存
            if($orderGoods->belongsToGood->reduce_stock_method != 1){
                $goodsOption->reduceStock($orderGoods->total);
            }
            $orderGoods->hasOneGoods->save();
            return $goodsOption->save();
        }
        /**
         * @var $goods Goods
         */
        $goods = $orderGoods->hasOneGoods;
        $goods->addSales($orderGoods->total);
        // 不是无限库存 减库存
        if($orderGoods->belongsToGood->reduce_stock_method != 1) {
            $goods->reduceStock($orderGoods->total);
        }

        return $goods->save();
    }
    public function subscribe($events)
    {
        $events->listen(
            AfterOrderCreatedEvent::class,
            self::class . '@onOrderCreated'
        );
        $events->listen(
            AfterOrderPaidEvent::class,
            self::class . '@onOrderPaid'
        );
    }
}