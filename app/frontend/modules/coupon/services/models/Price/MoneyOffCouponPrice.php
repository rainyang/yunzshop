<?php
/**
 * 立减优惠券
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/25
 * Time: 下午5:21
 */

namespace app\frontend\modules\coupon\services\models\Price;

use app\frontend\modules\goods\services\models\PreGeneratedOrderGoodsModel;

class MoneyOffCouponPrice extends CouponPrice
{
    public function valid()
    {
        //todo 商品价格中未使用优惠的金额 不小于 满减额度
        if (!float_lesser($this->getOrderGoodsGroupUnusedEnoughMoney(), $this->dbCoupon->enough)) {
            return true;
        }
        return false;
    }

    /**
     * 累加所有商品会员价
     * @return int
     */
    private function getOrderGoodsGroupPrice()
    {
        //会员价-折扣券优惠金额
        return $this->coupon->getOrderGoodsInScope()->getVipPrice() - $this->coupon->getOrderGoodsInScope()->getCouponDiscountPrice();
    }

    /**
     * 累加所有商品未使用优惠的金额
     * @return mixed
     */
    private function getOrderGoodsGroupUnusedEnoughMoney()
    {

        return $this->getOrderGoodsGroupPrice() - $this->coupon->getOrderGoodsInScope()->getOrderGoodsGroup()->sum('coupons.enough');
    }

    /**
     * 单件商品当前成交价
     * @param $orderGoods
     * @return mixed
     */
    private function getOrderGoodsPrice($orderGoods)
    {
        //之前的
        return $orderGoods->getVipPrice() - $orderGoods->couponDiscountPrice;
    }

    /**
     * 优惠券价格
     * @return mixed
     */
    public function getPrice()
    {
        return $this->dbCoupon->deduct;
    }

    /**
     * 分配优惠金额 立减折扣券使用 商品折扣后价格计算
     */
    public function setOrderGoodsDiscountPrice()
    {

        //dd($this->getOrderGoodsInScope());
        foreach ($this->coupon->getOrderGoodsInScope()->getOrderGoodsGroup() as $orderGoods) {
            /**
             * @var $orderGoods PreGeneratedOrderGoodsModel
             */
            //(优惠券金额/折扣优惠券后价格)*折扣优惠券后价格
//            dd($this->getPrice());
//            dd($this->getOrderGoodsGroupPrice());
//            dd($this->getOrderGoodsPrice($orderGoods));
//            exit;
            $orderGoods->couponMoneyOffPrice += number_format(($this->getPrice() / $this->getOrderGoodsGroupPrice()) * $this->getOrderGoodsPrice($orderGoods), 2);

        }
    }
}