<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/3
 * Time: 上午11:46
 */

namespace app\frontend\models\goods;


use app\frontend\models\OrderGoods;

/**
 * Class Sale
 * @package app\frontend\models\goods
 * @property string max_point_deduct
 */
class Sale extends \app\common\models\Sale
{
    /**
     * 计算满额减金额
     * @param $goods_price
     * @return mixed
     */
    public function getFullReductionAmount($goods_price)
    {
        if ($goods_price < $this->ed_full) {
            // 未满额
            return 0;
        }

        if ($this->ed_reduction < 0 ) {
            // 减额非正数时,记录异常
            \Log::error('商品计算满减价格时,减额数据非正数', [$this->id, $this->ed_full, $this->ed_reduction]);
            return 0;
        }
        if (!($this->ed_reduction < $goods_price)) {
            // 减额大于商品价格时,记录异常
            \Log::error('商品计算满减价格时,减额大于商品价格', [$this->id, $this->ed_full, $this->ed_reduction]);
        }
        return min($this->ed_reduction, $goods_price);
    }

    /**
     * 是否包邮
     * @param OrderGoods $orderGoods
     * @return bool
     */
    public function isFree(OrderGoods $orderGoods)
    {
        if (!isset($orderGoods->order->orderAddress)) {
            //未选择地址时
            return false;
        }

        if (!$this->inFreeArea($orderGoods)) {
            //收货地址不在包邮区域
            return false;
        }

        return $this->enoughQuantity($orderGoods->total) || $this->enoughAmount($orderGoods->price);
    }

    /**
     * 收货地址不在包邮区域
     * @return bool
     */
    private function inFreeArea($orderGoods)
    {
        $ed_areaids = (explode(',', $this->ed_areaids));

        if (empty($ed_areaids)) {
            return true;
        }
        if (in_array($orderGoods->order->orderAddress->city_id, $ed_areaids)) {
            return false;
        }
        return true;
    }

    /**
     * 单商品购买数量
     * @param $total
     * @return bool
     */
    private function enoughQuantity($total)
    {
        if ($this->ed_num == false) {
            return false;
        }
        return $total >= $this->ed_num;
    }

    /**
     * 单商品价格
     * @param $price
     * @return bool
     */
    private function enoughAmount($price)
    {
        if ($this->ed_money == false) {
            return false;
        }

        return $price >= $this->ed_money;
    }
}