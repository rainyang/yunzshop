<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/11
 * Time: 上午10:00
 */

namespace app\frontend\modules\dispatch\listeners\prices;

use app\backend\modules\goods\models\Dispatch;
use app\common\events\dispatch\OrderDispatchWasCalculated;
use app\common\models\goods\GoodsDispatch;

class TemplateOrderDispatchPrice
{
    private $event;

    public function handle(OrderDispatchWasCalculated $event)
    {
        $this->event = $event;
        if (!$this->needDispatch()) {
            return;
        }
        $price = $event->getOrderModel()->getOrderGoodsModels()->sum(function ($orderGoods) {
            if ($orderGoods->hasOneGoodsDispatch->dispatch_type == GoodsDispatch::TEMPLATE_TYPE) {
                $this->getPrice($orderGoods);
                return $orderGoods->hasOneGoodsDispatch->dispatch_price;
            }
            return 0;
        });
        //返回给事件
        $event->addData(['price'=>$price]);
        return;
    }

    private function getPrice($orderGoods)
    {
        $dispatch = $orderGoods->hasOneGoodsDispatch;
        if (empty($dispatch->dispatch_id)) {
            $dispatch = Dispatch::getOneByDefault();
        } else {
            $dispatch = Dispatch::getOne($dispatch->dispatch_id);
        }
        //存不存在都没有的情况
        if ($dispatch) {
            if ($dispatch->calculate_type == 1) {
                if ($orderGoods->total > $dispatch->first_piece) {
                    $price = $dispatch->first_piece_price + ceil(($orderGoods->total - $dispatch->first_piece) / $dispatch->another_piece) * $dispatch->another_piece_price;
                } else {
                    $price = $dispatch->first_piece_price;
                }
            } else if ($dispatch->calculate_type == 0) {
                echo '<pre>';print_r($orderGoods->hasOneGoods->weight);exit;
                if ($orderGoods->hasOneGoods->weight <= 0) {
                    $price = 0;
                }
                $weight = $orderGoods->hasOneGoods->weight * $orderGoods->total;
                if ($weight > $dispatch->first_weight) {
                    $price = $dispatch->first_weight_price + ceil(($weight - $dispatch->first_weight) / $dispatch->another_weight) * $dispatch->another_weight_price;
                } else {
                    $price = $dispatch->first_weight_price;
                }
            }
            echo '<pre>';print_r($price);exit;
        }
    }

    //订单满足本插件 todo 需要重写
    public function needDispatch()
    {
        return true;
    }

}