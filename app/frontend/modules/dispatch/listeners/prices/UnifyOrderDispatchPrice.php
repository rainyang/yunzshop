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

    //订单满足本插件 todo 需要重写
    public function needDispatch()
    {
        return true;
    }

}