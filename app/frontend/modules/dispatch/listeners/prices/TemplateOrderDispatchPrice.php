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
    private $order;

    public function handle(OrderDispatchWasCalculated $event)
    {
        $this->event = $event;
        $this->order = $event->getOrderModel();
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

    private function verify($price)
    {
        if (empty($price)) {
            return 0;
        }
        return $price;
    }

    /**
     * 数量
     */
    private function calculationByPiece($orderGoods)
    {
        // 订单商品总重
        $goods_total = $orderGoods->total;

        $piece_data = unserialize($this->dispatch->piece_data);

        // 存在重量数据
        if ($piece_data) {
            $dispatch = '';

            // 根据配送地址匹配区域数据
            $city = isset($this->order->orderAddress['city']) ? $this->order->orderAddress['city'] : '';
            if (!$city) {
                return 0;
            }
            $city_id = Address::where('areaname', $city)->value('id');
            foreach ($piece_data as $key => $piece) {
                $area_ids = explode(';', $piece['area_ids']);
                if (in_array($city_id, $area_ids)) {
                    $dispatch = $piece;
                    break;
                }
            }

            if ($dispatch) {
                // 找到匹配的数量数据
                if ($goods_total > $dispatch['first_piece']) {

                    return $dispatch['first_piece_price'] + ceil(($goods_total - $dispatch['first_piece_price']) / $dispatch['first_piece_price']) * $dispatch['first_piece_price'];
                } else {
                    return $dispatch['first_piece_price'];
                }
            }
        }

        // 默认全国重量运费
        if ($orderGoods->total > $this->dispatch->first_piece) {
            return $this->dispatch->first_piece_price + ceil(($orderGoods->total - $this->dispatch->first_piece) / $this->dispatch->another_piece) * $this->dispatch->another_piece_price;
        } else {
            return $this->dispatch->first_piece_price;
        }
    }

    /**
     * 根据重量计算运费
     */
    private function calculationByWeight($orderGoods)
    {
        // 订单商品总重
        $weight_total = $orderGoods->getWeight() * $orderGoods->total;

        $weight_data = unserialize($this->dispatch->weight_data);
        // 存在重量数据
        if ($weight_data) {
            $dispatch = '';

            // 根据配送地址匹配区域数据
            $city_id = isset($this->order->orderAddress['city_id']) ? $this->order->orderAddress['city_id'] : '';
            if (!$city_id) {
                return 0;
            }

            foreach ($weight_data as $key => $weight) {
                //dd($weight['area_ids']);
                $area_ids = explode(';', $weight['area_ids']);
                if (in_array($city_id, $area_ids)) {
                    $dispatch = $weight;
                    break;
                }
            }

            if ($dispatch) {
                // 找到匹配的重量数据
                if ($weight_total > $dispatch['first_weight']) {
                    // 续重:   首重价格+(重量-首重)/续重*续重价格
                    // 20 + (500 - 400)
                    return $dispatch['first_weight_price'] + ceil(($weight_total - $dispatch['first_weight']) / $dispatch['another_weight']) * $dispatch['another_weight_price'];
                } else {
                    return $dispatch['first_weight_price'];
                }
            }
        }

        // 默认全国重量运费
        if ($weight_total > $this->dispatch->first_weight) {
            return $this->dispatch->first_weight_price + floor(($weight_total - $this->dispatch->first_weight) / $this->dispatch->another_weight) * $this->dispatch->another_weight_price;
        } else {
            return $this->dispatch->first_weight_price;
        }

    }


}