<?php
namespace app\frontend\modules\goods\listeners;
use app\common\events\order\AfterOrderCreatedEvent;
use app\common\events\order\AfterOrderPaidEvent;
use app\common\models\OrderGoods;
use app\frontend\modules\goods\models\Goods;
use app\frontend\modules\goods\models\GoodsOption;

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/14
 * Time: 下午10:34
 */
class GoodsStock
{
    public function onOrderCreated(AfterOrderCreatedEvent $event){

        $order = $event->getOrderModel();
        $order->hasManyOrderGoods->map(function ($orderGoods){
            if($orderGoods->goods->reduce_stock_method != 0){
                return false;
            }
            $this->reduceStock($orderGoods);
        });
    }
    public function onOrderPaid(AfterOrderPaidEvent $event){

        $order = $event->getOrderModel();
        $order->hasManyOrderGoods->map(function ($orderGoods){
            if($orderGoods->goods->reduce_stock_method != 1){
                return false;
            }
            $this->reduceStock($orderGoods);
        });
    }
    private function reduceStock($orderGoods){
        /**
         * @var $orderGoods OrderGoods
         */
        if($orderGoods->isOption()){
            $goods_option = $orderGoods->goodsOption;
            /**
             * @var $goods_option GoodsOption
             */
            $goods_option->reduceStock($orderGoods->total);
            $goods_option->save();
        }
        /**
         * @var $goods Goods
         */
        $goods = $orderGoods->hasOneGoods;

        $goods->reduceStock($orderGoods->total);
        $goods->save();
    }
    public function subscribe($events)
    {
        $events->listen(
            AfterOrderCreatedEvent::class,
            self::class . '@onOrderCreated'
        );
        $events->listen(
            AfterOrderPaidEvent::class,
            self::class . '@onOrderCreated'
        );
    }
}