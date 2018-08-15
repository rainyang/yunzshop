<?php
/**
 * 未生成的订单商品类
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/28
 * Time: 下午1:44
 */

namespace app\frontend\modules\orderGoods\models;

use app\frontend\modules\deduction\OrderGoodsDeductionCollection;
use app\frontend\modules\orderGoods\price\option\NormalOrderGoodsPrice;

trait PreOrderGoodsTrait
{
    protected $priceCalculator;
    /**
     * 获取生成前的模型属性
     * @return array
     */
    public function getPreAttributes()
    {
        $attributes = array(
            'goods_id' => $this->goods->id,
            'goods_sn' => $this->goods->goods_sn,
            'total' => $this->total,
            'title' => $this->goods->title,
            'thumb' => yz_tomedia($this->goods->thumb),
            'goods_price' => $this->getGoodsPrice(),
            'price' => $this->getPrice(),
            'goods_cost_price' => $this->getGoodsCostPrice(),
            'goods_market_price' => $this->getGoodsMarketPrice(),


        );

        if ($this->isOption()) {

            $attributes += [
                'goods_option_id' => $this->goodsOption->id,
                'goods_option_title' => $this->goodsOption->title,
            ];
        }

        $attributes = array_merge($this->getAttributes(), $attributes);

        return $attributes;
    }

    /**
     * 获取利润
     * @return mixed
     */
    public function getGoodsCostPrice()
    {
        return $this->getPriceCalculator()->getGoodsCostPrice();

    }

    /**
     * 市场价
     * @return mixed
     */
    public function getGoodsMarketPrice()
    {
        return $this->getPriceCalculator()->getGoodsMarketPrice();

    }

    /**
     * 订单商品抵扣集合
     * @return OrderGoodsDeductionCollection
     */
    public function getOrderGoodsDeductions()
    {
        return $this->orderGoodsDeductions;
    }

    /**
     * 获取重量
     * @return mixed
     */
    public function getWeight()
    {
        if ($this->isOption()) {
            return $this->goodsOption->weight;
        }
        return $this->goods->weight;
    }

    /**
     * 成交价格
     * @return mixed
     */
    public function getPrice()
    {
        return $this->getPriceCalculator()->getPrice();
    }

    /**
     * 原始价格
     * @return mixed
     */
    public function getGoodsPrice()
    {
        return $this->getPriceCalculator()->getGoodsPrice();
    }

    /**
     * 获取价格计算者
     * @return NormalOrderGoodsPrice
     */
    protected function getPriceCalculator()
    {
        if (!isset($this->priceCalculator)) {
            $this->priceCalculator = $this->_getPriceCalculator();
        }
        return $this->priceCalculator;
    }
}