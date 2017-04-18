<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/3/11
 * Time: 上午17:10
 */

namespace app\frontend\modules\dispatch\listeners\prices;

use app\backend\modules\goods\models\Dispatch;
use app\common\events\dispatch\OrderDispatchWasCalculated;
use app\common\models\goods\GoodsDispatch;

class TemplateOrderDispatchPrice
{
    private $event;
    private $dispatch;

    public function handle(OrderDispatchWasCalculated $event)
    {
        $this->event = $event;
        $price = $event->getOrderModel()->getOrderGoodsModels()->sum(function ($orderGoods) {
            if ($orderGoods->hasOneGoodsDispatch->dispatch_type == GoodsDispatch::TEMPLATE_TYPE) {
                return $this->getPrice($orderGoods);
            }
            return 0;
        });
        //返回给事件
        $event->addData(['price'=>$price]);
        return;
    }

    private function getPrice($orderGoods)
    {
        $this->dispatch = $orderGoods->hasOneGoodsDispatch;
        if (empty($this->dispatch->dispatch_id)) {
            $this->dispatch = Dispatch::getOneByDefault();
        } else {
            $this->dispatch = Dispatch::getOne($this->dispatch->dispatch_id);
        }
        //存不存在都没有的情况
        return $this->calculation($orderGoods);
    }

    private function calculation($orderGoods)
    {
        $price = 0;
        if (!$this->dispatch) {
            return $price;
        }
        switch ($this->dispatch->calculate_type) {
            case 1:
                $price = $this->calculationByPiece($orderGoods);
                break;
            case 0:
                $price = $this->calculationByWeight($orderGoods);
                break;
        }
        $price = $this->verify($price);
        return $price;
    }

    private function calculationByPiece($orderGoods)
    {
        if ($orderGoods->total > $this->dispatch->first_piece) {
            return $this->dispatch->first_piece_price + ceil(($orderGoods->total - $this->dispatch->first_piece) / $this->dispatch->another_piece) * $this->dispatch->another_piece_price;
        } else {
            return $this->dispatch->first_piece_price;
        }
    }

    private function calculationByWeight($orderGoods)
    {
        $weight = $orderGoods->hasOneGoods->weight * $orderGoods->total;
        if ($weight > $this->dispatch->first_weight) {
            return $this->dispatch->first_weight_price + ceil(($weight - $this->dispatch->first_weight) / $this->dispatch->another_weight) * $this->dispatch->another_weight_price;
        } else {
            return $this->dispatch->first_weight_price;
        }
    }

    private function verify($price)
    {
        if (empty($price)) {
            return 0;
        }
        return $price;
    }
}