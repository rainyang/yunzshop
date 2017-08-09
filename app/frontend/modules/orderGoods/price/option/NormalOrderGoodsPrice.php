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
    /**
     * 支付价
     */
    public function getPayAmount()
    {
        // todo 成交价-抵扣金额(均摊)
    }

    /**
     * 获取计算中的成交价
     * @return mixed
     */
    public function getCalculationPrice(){
        return $this->goodsPrice;
    }
    /**
     * 成交价
     * @return mixed
     */
    public function getPrice()
    {
        $this->goodsPrice = max($this->getFinalPrice(),0);
        $this->goodsPrice = max($this->goodsPrice - $this->getFullReductionAmount(),0);
        $this->goodsPrice = max($this->goodsPrice - $this->getDiscountAmount(),0);
        return $this->goodsPrice;
    }

    /**
     * 优惠金额
     * @return int
     */
    public function getDiscountAmount()
    {
        return $this->getCouponAmount();
    }

    /**
     * 销售价
     * @return mixed
     */
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
        return $this->orderGoods->sale->getFullReductionAmount($this->goodsPrice);
    }
}