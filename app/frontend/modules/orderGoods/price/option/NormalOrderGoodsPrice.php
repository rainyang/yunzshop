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
     * @var float
     */
    private $paymentAmount;
    /**
     * @var float
     */
    private $deductionAmount;
    private $deductionKey;
    /**
     * @var float
     */
    private $price;


    /**
     * 获取商品的模型,规格继承时复写这个方法
     * @return mixed
     */
    protected function goods()
    {
        return $this->orderGoods->goods;
    }

    /**
     * 商品的原价,为了规格继承时将属性名替换掉
     * @return mixed
     */
    protected function aGoodsPrice()
    {
        return $this->goods()->price;
    }

    /**
     * 成交价
     * @return mixed
     */
    public function getPrice()
    {
        if (isset($this->price)) {
            return $this->price;
        }
        // 商品销售价 - 等级优惠金额 - 单品满减优惠金额
        $this->price = $this->getGoodsPrice();
        $this->price -= $this->getVipDiscountAmount();

        $this->price = max($this->price, 0);
        return $this->price;
    }

    /**
     * 获取订单商品支付金额
     * @return float|mixed
     */
    public function getPaymentAmount()
    {
        if (isset($this->paymentAmount)) {
            return $this->paymentAmount;
        }
        $this->paymentAmount = $this->getPrice();

        $this->paymentAmount -= $this->getSingleEnoughReduceAmount();
        $this->paymentAmount -= $this->getEnoughReduceAmount();

        $this->paymentAmount -= $this->getCouponAmount();
        $this->paymentAmount -= $this->getDeductionAmount();

        $this->paymentAmount = max($this->paymentAmount, 0);
        $result = $this->paymentAmount;
        unset($this->paymentAmount);
        return $result;
    }

    /**
     * 获取订单商品抵扣金额
     * @return float
     */
    public function getDeductionAmount()
    {

        if($this->deductionKey != $this->orderGoods->getOrderGoodsDeductions()->toJson()){
            $this->deductionKey = $this->orderGoods->getOrderGoodsDeductions()->toJson();
            $this->deductionAmount = $this->orderGoods->getOrderGoodsDeductions()->getUsedPoint()->getMoney();

        }
        return $this->deductionAmount;
    }

    /**
     * 销售价(商品的原销售价)
     * @return mixed
     */
    public function getGoodsPrice()
    {
        return $this->aGoodsPrice() * $this->orderGoods->total;
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
        return $this->goods()->cost_price * $this->orderGoods->total;
    }

    /**
     * 市场价
     * @return mixed
     */
    public function getGoodsMarketPrice()
    {
        return $this->goods()->market_price * $this->orderGoods->total;
    }

    /**
     * 单品满减
     * @return float|int
     */
    private function getSingleEnoughReduceAmount()
    {
        return $this->singleEnoughReduce->getAmount();
    }

    /**
     * 全场满减
     * @return float|int
     */
    private function getEnoughReduceAmount()
    {

        return $this->enoughReduce->getAmount();
    }

    /**
     * 商品的会员等级折扣金额
     * @return mixed
     */
    public function getVipDiscountAmount()
    {
        return $this->goods()->getVipDiscountAmount() * $this->orderGoods->total;
    }

}