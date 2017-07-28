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
        $result = max($this->getFinalPrice() - $this->getFullPriceReductions() - $this->getDiscountPrice(), 0);
        return $result;
    }

    public function getDiscountPrice()
    {
        return $this->orderGoodsPriceCalculator->getCouponPrice();
    }

    public function getGoodsPrice()
    {
        return $this->orderGoods->goods->price * $this->orderGoods->total;
    }

    //todo 此处混乱
    public function getFinalPrice()
    {
        return $this->orderGoods->goods->finalPrice * $this->orderGoods->total - $this->orderGoods->sale->getFullPriceReductions($this->orderGoods->goods->finalPrice * $this->orderGoods->total);
    }

    public function getCouponPrice()
    {
        if (!isset($this->orderGoods->coupons)) {
            return 0;
        }

        return $this->orderGoods->coupons->sum('amount');
    }

    public function getGoodsCostPrice()
    {
        return $this->orderGoods->goods->cost_price * $this->orderGoods->total;
    }

    public function getGoodsMarketPrice()
    {
        return $this->orderGoods->goods->market_price * $this->orderGoods->total;
    }

    //todo 此处混乱
    public function getFullPriceReductions()
    {
        if (!isset($this->orderGoods->sale)) {
            return 0;
        }
        return $this->orderGoods->sale->getFullPriceReductions($this->getFinalPrice());
    }
}