<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/11
 * Time: 上午17:10
 */

namespace app\frontend\modules\dispatch\listeners\prices;

use app\backend\modules\goods\models\Dispatch;
use app\common\events\dispatch\OrderDispatchWasCalculated;
use app\common\models\goods\GoodsDispatch;
use app\common\models\Address;
use app\frontend\models\OrderGoods;

class TemplateOrderDispatchPrice
{
    private $event;
    private $dispatch;

    public function handle(OrderDispatchWasCalculated $event)
    {
        $this->event = $event;
        $price = $event->getOrderModel()->getOrderGoodsModels()->sum(function ($orderGoods) {
            /**
             * @var $orderGoods OrderGoods
             */
            if ($orderGoods->isFreeShipping()) {
                return 0;
            }
            if (!isset($orderGoods->hasOneGoodsDispatch)) {
                return 0;
            }
            if ($orderGoods->hasOneGoodsDispatch->dispatch_type == GoodsDispatch::TEMPLATE_TYPE) {
                return $this->getPrice($orderGoods);
            }
            return 0;
        });
        $data = [
            'price' => $price,
            'name' => '运费模板',
        ];
        //返回给事件
        $event->addData($data);
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
        $weight_data = unserialize($this->dispatch->weight_data);
        if ($weight_data) {
            $address = json_decode(\YunShop::request()->address, true);
            if ($address['city']) {
                return $this->areaDispatchPrice($address['city'], $weight_data, $weight);
            } else {
                return 0;
            }
        } else {
            if ($weight > $this->dispatch->first_weight) {
                return $this->dispatch->first_weight_price + ceil(($weight - $this->dispatch->first_weight) / $this->dispatch->another_weight) * $this->dispatch->another_weight_price;
            } else {
                return $this->dispatch->first_weight_price;
            }
        }
    }

    private function verify($price)
    {
        if (empty($price)) {
            return 0;
        }
        return $price;
    }

    private function areaDispatchPrice($city, $weight_data, $weight_total)
    {
        $dispatch = '';
        $city_id = Address::where('areaname', $city)->value('id');
        foreach ($weight_data as $key => $weight) {
            $area_ids = explode(';', $weight['area_ids']);
            if (in_array($city_id, $area_ids)) {
                $dispatch = $weight;
                break;
            }
        }
        if ($dispatch) {
            if ($weight_total > $dispatch['first_weight']) {
                return $dispatch['first_weight_price'] + ceil(($weight_total - $dispatch['first_weight']) / $dispatch['another_weight']) * $dispatch['another_weight_price'];
            } else {
                return $dispatch['first_weight_price'];
            }
        }
        return 0;
    }
}