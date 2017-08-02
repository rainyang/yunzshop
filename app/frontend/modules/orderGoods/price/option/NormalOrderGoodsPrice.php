<?php

namespace app\frontend\modules\orderGoods\price\option;

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/5/19
 * Time: 下午6:04
 */
class NormalOrderGoodsPrice extends OrderGoodsPrice
{
    //todo 此处混乱
    public function getPrice()
    {
        //成交价格=商品销售价-单品满减-优惠价格
        $result = max($this->getFinalPrice() - $this->getFullReductionAmount() - $this->getDiscountAmount(), 0);
        return $result;
    }

    public function getDiscountAmount()
    {
        return $this->getCouponAmount();
    }

    public function getGoodsPrice()
    {
        return $this->orderGoods->goods->price * $this->orderGoods->total;
    }

    /**
     * @return mixed
     */
    public function getFinalPrice()
    {
        $fullPrice = isset($this->orderGoods->sale) ? $this->orderGoods->sale->getFullReductionAmount($this->orderGoods->goods->finalPrice * $this->orderGoods->total) : 0;
        return $this->orderGoods->goods->finalPrice * $this->orderGoods->total - $fullPrice;
    }

    /**
     * 优惠券价
     * @return int
     */
    public function getCouponAmount()
    {
        if (!isset($this->orderGoods->coupons)) {
            return 0;
        }

        return $this->orderGoods->coupons->sum('amount');
    }

    /**
     * 成本价
     * @return mixed
     */
    public function getGoodsCostPrice()
    {
        return $this->orderGoods->goods->cost_price * $this->orderGoods->total;
    }

    /**
     * 市场价
     * @return mixed
     */
    public function getGoodsMarketPrice()
    {
        return $this->orderGoods->goods->market_price * $this->orderGoods->total;
    }

    /**
     * 单品满减
     * @return int
     */
    public function getFullReductionAmount()
    {
        if (!isset($this->orderGoods->sale)) {
            return 0;
        }
        return $this->orderGoods->sale->getFullReductionAmount($this->getFinalPrice());
    }
}