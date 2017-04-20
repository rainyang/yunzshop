<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/11
 * Time: 上午10:00
 */

namespace app\frontend\modules\dispatch\listeners\prices;

use app\common\events\dispatch\OrderDispatchWasCalculated;
use app\common\models\goods\GoodsDispatch;
use app\frontend\modules\order\services\OrderService;

class UnifyOrderDispatchPrice
{
    private $event;

    public function handle(OrderDispatchWasCalculated $event)
    {
        $this->event = $event;
        if (!$this->needDispatch()) {
            return;
        }
        $price = $event->getOrderModel()->getOrderGoodsModels()->max(function ($orderGoods) {
            if ($orderGoods->hasOneGoodsDispatch->dispatch_type == GoodsDispatch::UNIFY_TYPE) {
                return $orderGoods->hasOneGoodsDispatch->dispatch_price;
            }
            return 0;
        });

        //返回给事件
        $event->addData(['price'=>$price]);
        return;
    }

    private function needDispatch()
    {
        $allGoodsIsReal = OrderService::allGoodsIsReal($this->event->getOrderModel()->getOrderGoodsModels());

        if($allGoodsIsReal){
            return true;
        }

        return false;
    }

}