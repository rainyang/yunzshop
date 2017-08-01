<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/11
 * Time: 上午10:00
 */

namespace app\frontend\modules\dispatch\listeners\prices;

use app\common\events\dispatch\OrderDispatchWasCalculated;
use app\common\models\goods\GoodsDispatch;
use app\frontend\models\OrderGoods;
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
            /**
             * @var $orderGoods OrderGoods
             */

            if($orderGoods->isFreeShipping())
            {
                return 0;
            }
            if(!isset($orderGoods->hasOneGoodsDispatch)){
                return 0;
            }
            if ($orderGoods->hasOneGoodsDispatch->dispatch_type == GoodsDispatch::UNIFY_TYPE) {
                return $orderGoods->hasOneGoodsDispatch->dispatch_price;
            }
            return 0;
        });
        $data = [
            'price' => $price,
            'name' => '统一运费',
        ];
        //返回给事件
        $event->addData($data);
        return;
    }

    private function needDispatch()
    {

        if ($this->event->getOrderModel()->is_virtual) {
            return false;
        }

        return true;
    }

}