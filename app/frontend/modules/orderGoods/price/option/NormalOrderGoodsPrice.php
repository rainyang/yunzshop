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
     * 获取商品的模型,规格继承时复写这个方法
     * @return mixed
     */
    protected function goods()
    {
        return $this->orderGoods->goods;
    }

    /**
     * 商品的原价,为了规格继承时将属性名替换掉
     * @return float
     */
    protected function aGoodsPrice()
    {
        return $this->goods()->price;
    }

    /**
     * 成交价(计算了间接优惠,原本为了方便分销分红等插件使用,但现在这个价格是动态设置的需要实时计算,所以没意义了)
     * @return float
     */
    public function getPrice()
    {
        // 商品销售价 - 等级优惠金额  - 单品满减优惠金额
        return max($this->getGoodsPrice() - $this->getVipDiscountAmount() - $this->getFullReductionAmount(), 0);
    }

    /**
     * @return float
     */
    public function getPrivatePrice()
    {
        //
        return $this->getPrice() - $this->getDiscountAmount() - $this->getDeductionAmount();
    }

    /**
     *
     * @return float
     */
    public function getDeductionAmount()
    {
        if (!isset($this->orderGoods->orderGoodsDeductions)) {
            return 0;
        }

        return $this->orderGoods->orderGoodsDeductions->sum('amount');
    }

    /**
     * 优惠金额(只计算了优惠券的间接优惠金额)
     * @return float
     */
    public function getDiscountAmount()
    {
        return $this->getCouponAmount();
    }

    /**
     * 销售价(商品的原销售价)
     * @return float
     */
    public function getGoodsPrice()
    {
        return $this->aGoodsPrice() * $this->orderGoods->total;
    }

    /**
     * 优惠券价
     * @return float
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
     * @return float
     */
    public function getGoodsCostPrice()
    {
        return $this->goods()->cost_price * $this->orderGoods->total;
    }

    /**
     * 市场价
     * @return float
     */
    public function getGoodsMarketPrice()
    {
        return $this->goods()->market_price * $this->orderGoods->total;
    }

    /**
     * 单品满减
     * @return float
     */
    public function getFullReductionAmount()
    {
        //dd($this->fullReductionAmount);
        //dd(isset($this->fullReductionAmount));

        if (isset($this->fullReductionAmount)) {
            //echo 1;
            return $this->fullReductionAmount;
        }
        if (!isset($this->orderGoods->sale)) {
            //echo 2;
            return 0;
        }
        $result = $this->orderGoods->sale->getFullReductionAmount($this->getGoodsPrice());

        $this->fullReductionAmount = $result;
        $this->setFullReductionOrderGoodsDiscount($result);
        return $result;
    }

    /**
     * 定义订单商品的单品满减优惠
     * @param $amount
     */
    private function setFullReductionOrderGoodsDiscount($amount)
    {
        $orderGoodsDiscount = new PreOrderGoodsDiscount([
            'discount_code' => 'fullReduction',
            'amount' => $amount,
            'name' => '单品满额减',
        ]);
        $orderGoodsDiscount->setOrderGoods($this->orderGoods);

    }

    /**
     * 商品的会员等级折扣金额
     * @return float
     */
    public function getVipDiscountAmount()
    {
        return $this->goods()->getVipDiscountAmount() * $this->orderGoods->total;
    }

}