<?php
/**
 * 立减优惠券
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/25
 * Time: 下午5:21
 */

namespace app\frontend\modules\coupon\services\models\Price;

use app\common\models\coupon\GoodsMemberCoupon;
use app\frontend\modules\orderGoods\models\PreOrderGoods;

class MoneyOffCouponPrice extends CouponPrice
{
    /**
     * 累加所有商品会员价
     * @return int
     */
    protected function getOrderGoodsCollectionPrice()
    {
        //会员价-折扣券优惠金额
        return $this->coupon->getOrderGoodsInScope()->getPaymentAmount();
    }

    /**
     * 单件商品当前成交价
     * @param PreOrderGoods $orderGoods
     * @return mixed
     * @throws \app\common\exceptions\ShopException
     */
    private function getOrderGoodsPrice(PreOrderGoods $orderGoods)
    {
        //之前的
        return $orderGoods->getPrice() - $orderGoods->couponDiscountPrice;
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
        if($this->isSet){
            return;
        }
        //dd($this->getOrderGoodsInScope());
        $this->coupon->getOrderGoodsInScope()->map(function ($orderGoods) {
            /**
             * @var $orderGoods PreOrderGoods
             */
            //(优惠券金额/折扣优惠券后价格)*折扣优惠券后价格
//            dd($this->getPrice());
//            dd($this->getOrderGoodsCollectionPrice());
//            dd($this->getOrderGoodsPrice($orderGoods));
//            exit;

            $goodsMemberCoupon = new GoodsMemberCoupon();
            $goodsMemberCoupon->amount = $this->getOrderGoodsPrice($orderGoods) / $this->getOrderGoodsCollectionPrice() * $this->getPrice();
            $goodsMemberCoupon->enough = $this->getOrderGoodsPrice($orderGoods) / $this->getOrderGoodsCollectionPrice() * $this->dbCoupon->enough;
            //todo 需要按照订单方式修改
            if (!isset($orderGoods->coupons)) {
                $orderGoods->coupons = collect();
            }

            $orderGoods->coupons->push($goodsMemberCoupon);

            //$orderGoods->setRelation('coupon',$goodsMemberCoupon);
        });
        $this->isSet = true;
    }

}