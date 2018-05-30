<?php

namespace app\frontend\modules\orderGoods\price\option;

use app\frontend\models\orderGoods\PreOrderGoodsDiscount;

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
    private $price;
    /**
     * @var float
     */
    private $fullReductionAmount;

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
     * @return float
     */
    public function getPaymentAmount()
    {
        if (isset($this->paymentAmount)) {
            return $this->paymentAmount;
        }
        $this->paymentAmount = $this->getPrice();

        $this->paymentAmount -= $this->getSingleEnoughReduceAmount();
        $this->paymentAmount -= $this->getFullReductionAmount();

        $this->paymentAmount -= $this->getDiscountAmount();
        $this->paymentAmount -= $this->getDeductionAmount();

        $this->paymentAmount = max($this->paymentAmount, 0);
        return $this->paymentAmount;
    }

    /**
     * 获取订单商品抵扣金额
     * @return float
     */
    public function getDeductionAmount()
    {
        return $this->orderGoods->getOrderGoodsDeductions()->getUsedPoint()->getMoney();
    }

    /**
     * 优惠金额(只计算了优惠券的间接优惠金额)
     * @return int
     */
    public function getDiscountAmount()
    {
        return $this->getCouponAmount();
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
     * 单品满减 todo
     */
    private function getSingleEnoughReduceAmount()
    {
        return 0.0;
    }

    /**
     * 全场满减 todo
     * @return int
     */
    private function getFullReductionAmount()
    {

        return 0.0;
    }

    /**
     * 定义订单商品的单品满减优惠
     * @param $amount
     */
    private function setFullReductionOrderGoodsDiscount($amount)
    {
        if (empty($amount)) {
            return;
        }
        $orderGoodsDiscount = new PreOrderGoodsDiscount([
            'discount_code' => 'fullReduction',
            'amount' => $amount,
            'name' => '单品满额减',
        ]);
        $orderGoodsDiscount->setOrderGoods($this->orderGoods);

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