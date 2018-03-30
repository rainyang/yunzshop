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


class TemplateOrderDispatchPrice
{
    private $event;


    private $order;




    private $goodsModel;



    public function handle(OrderDispatchWasCalculated $event) {

        $this->event = $event;

        $this->order = $event->getOrderModel();

        $goodsModels = $event->getOrderModel()->getOrderGoodsModels();

        $price = $this->getPrice($goodsModels);

        //dd($price);
        $data = [
            'price' => $price,
            'type'  => GoodsDispatch::TEMPLATE_TYPE,
            'name'  => '运费模板',
        ];


        //返回给事件
        $event->addData($data);
        return;
    }


    private function getPrice($goodsModels)
    {
        $dispatch_prices = [];
        $dispatch_ids = $this->getDispatchIds($goodsModels);

        foreach ($dispatch_ids as $dispatch_id) {
            $dispatch_prices[] = $this->getDispatchPrice($dispatch_id, $goodsModels);
        }


        return max($dispatch_prices);
    }


    /**
     * 通过订单商品集合 获取所用到的配送模版 ID 集
     *
     * @param $goodsModels
     * @return array
     */
    private function getDispatchIds($goodsModels)
    {
        $dispatch_ids = [];
        foreach ($goodsModels as $goodsModel) {

            $goodsDispatch = $goodsModel->hasOneGoodsDispatch;

            if ($goodsDispatch->dispatch_type == GoodsDispatch::TEMPLATE_TYPE) {

                $dispatch_id = $goodsDispatch->dispatch_id;
                if (empty($dispatch_id)) {
                    $goodsDispatch->dispatch_id = $this->getDefaultDispatchId();
                }

                if (!in_array($dispatch_id, $dispatch_ids)) {
                    $dispatch_ids[] = $goodsDispatch->dispatch_id;
                }
            }
        }

        return $dispatch_ids;
    }


    private function getDefaultDispatchId()
    {
        $defaultDispatch = Dispatch::getOneByDefault();

        //todo 如果没有默认配送模版 如何处理

        return $defaultDispatch->id ?: 0;
    }


    private function getDispatchPrice($dispatch_id, $goodsModels)
    {

        $dispatch_good_total = 0;
        $dispatch_good_weight = 0;


        foreach ($goodsModels as $goodsModel) {

            //商品满额、满件减免运费
            if ($goodsModel->isFreeShipping()) {
                continue;
            }

            $dispatchModel = $goodsModel->hasOneGoodsDispatch;

            //配送模版不存在
            if (!isset($dispatchModel)) {
                continue;
            }

            //如果是默认配送模版
            if (!$dispatchModel->dispatch_id){
                $dispatchModel->dispatch_id = $this->getDefaultDispatchId();
            }

            if ($dispatchModel->dispatch_type != GoodsDispatch::TEMPLATE_TYPE) {
                continue;
            }

            if ($dispatchModel->dispatch_id != $dispatch_id) {
                continue;
            }

            $dispatch_good_total += $goodsModel->total;
            $dispatch_good_weight += $goodsModel->getWeight() * $goodsModel->total;
        }

        /*dump($dispatch_good_total);
        dump($dispatch_good_weight);
        dd($dispatch_id);*/
        return $this->calculation($dispatch_id, $dispatch_good_total, $dispatch_good_weight);
    }



    private function calculation($dispatch_id, $dispatch_good_total, $dispatch_good_weight)
    {
        $price = 0;
        if (!$dispatch_id) {
            return $price;
        }

        $dispatchModel = Dispatch::getOne($dispatch_id);

        if (!$dispatch_id) {
            return $price;
        }

        switch ($dispatchModel->calculate_type) {
            case 1:
                $price = $this->calculationByPiece($dispatchModel, $dispatch_good_total);
                break;
            case 0:
                $price = $this->calculationByWeight($dispatchModel, $dispatch_good_weight);
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


    private function calculationByPiece($dispatchModel, $goods_total)
    {
        if (!$goods_total) {
            return 0;
        }
        $piece_data = unserialize($dispatchModel->piece_data);

        // 存在
        if ($piece_data) {
            $dispatch = '';

            // 根据配送地址匹配区域数据
            $city_id = isset($this->order->orderAddress->city_id) ? $this->order->orderAddress->city_id : 0;

            if (!$city_id) {
                return 0;
            }
            foreach ($piece_data as $key => $piece) {
                $area_ids = explode(';', $piece['area_ids']);
                if (in_array($this->order->orderAddress->city_id, $area_ids)) {
                    $dispatch = $piece;
                    break;
                }
            }

            if ($dispatch) {
                // 找到匹配的数量数据
                if ($goods_total > $dispatch['first_piece']) {
                    $diff = $goods_total - $dispatch['first_piece'];
                    $another_piece = $dispatch['another_piece_price'];
                    if ($diff > 0) {
                        $another_piece = ceil($diff / $dispatch['another_piece']) * $dispatch['another_piece_price'];
                    }
                    return $dispatch['first_piece_price'] + $another_piece;
                } else {
                    return $dispatch['first_piece_price'];
                }
            }
        }

        // 默认件数
        if ($goods_total > $dispatchModel->first_piece) {
            $diff = $goods_total - $dispatchModel->another_piece;
            $another_piece = $dispatchModel->another_piece_price;
            if ($diff > 0) {
                $another_piece = ceil($diff / $dispatchModel->another_piece) * $dispatchModel->another_piece_price;
            }
            return $dispatchModel->first_piece_price + $another_piece;
        } else {
            return $dispatchModel->first_piece_price;
        }
    }

    private function calculationByWeight($dispatchModel, $weight_total)
    {
        if (!$weight_total) {
            return 0;
        }
        $weight_data = unserialize($dispatchModel->weight_data);

        // 存在重量数据
        if ($weight_data) {
            $dispatch = '';

            // 根据配送地址匹配区域数据
            $city_id = isset($this->order->orderAddress->city_id) ? $this->order->orderAddress->city_id : '';
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
        if ($weight_total > $dispatchModel->first_weight) {
            return $dispatchModel->first_weight_price + ceil(($weight_total - $dispatchModel->first_weight) / $dispatchModel->another_weight) * $dispatchModel->another_weight_price;
        } else {
            return $dispatchModel->first_weight_price;
        }
    }






    /*public function handle(OrderDispatchWasCalculated $event)
    {
        $this->event = $event;
        $this->order = $event->getOrderModel();
        $price = $event->getOrderModel()->getOrderGoodsModels()->sum(function ($orderGoods) {

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
            'type' => GoodsDispatch::TEMPLATE_TYPE,
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


    private function calculationByPiece($orderGoods)
    {
        // 件数
        $goods_total = $orderGoods->total;

        $piece_data = unserialize($this->dispatch->piece_data);
        // 存在
        if ($piece_data) {
            $dispatch = '';

            // 根据配送地址匹配区域数据
            $city_id = isset($this->order->orderAddress->city_id) ? $this->order->orderAddress->city_id : 0;

            if (!$city_id) {
                return 0;
            }
            foreach ($piece_data as $key => $piece) {
                $area_ids = explode(';', $piece['area_ids']);
                if (in_array($this->order->orderAddress->city_id, $area_ids)) {
                    $dispatch = $piece;
                    break;
                }
            }

            if ($dispatch) {
                // 找到匹配的数量数据
                if ($goods_total > $dispatch['first_piece']) {
                    $diff = $goods_total - $dispatch['first_piece'];
                    $another_piece = $dispatch['another_piece_price'];
                    if ($diff > 0) {
                        $another_piece = ceil($diff / $dispatch['another_piece']) * $dispatch['another_piece_price'];
                    }
                    return $dispatch['first_piece_price'] + $another_piece;
                } else {
                    return $dispatch['first_piece_price'];
                }
            }
        }

        // 默认件数
        if ($goods_total > $this->dispatch->first_piece) {
            $diff = $goods_total - $this->dispatch->another_piece;
            $another_piece = $this->dispatch->another_piece_price;
            if ($diff > 0) {
                $another_piece = ceil($diff / $this->dispatch->another_piece) * $this->dispatch->another_piece_price;
            }
            return $this->dispatch->first_piece_price + $another_piece;
        } else {
            return $this->dispatch->first_piece_price;
        }
    }


    private function calculationByWeight($orderGoods)
    {
        // 订单商品总重
        $weight_total = $orderGoods->getWeight() * $orderGoods->total;

        $weight_data = unserialize($this->dispatch->weight_data);
        // 存在重量数据
        if ($weight_data) {
            $dispatch = '';

            // 根据配送地址匹配区域数据
            $city_id = isset($this->order->orderAddress->city_id) ? $this->order->orderAddress->city_id : '';
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
            return $this->dispatch->first_weight_price + ceil(($weight_total - $this->dispatch->first_weight) / $this->dispatch->another_weight) * $this->dispatch->another_weight_price;
        } else {
            return $this->dispatch->first_weight_price;
        }

    }*/


}