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
use app\frontend\modules\orderGoods\models\PreGeneratedOrderGoods;

class MoneyOffCouponPrice extends CouponPrice
{

    /**
     * 累加所有商品会员价
     * @return int
     */
    protected function getOrderGoodsGroupPrice()
    {
        //会员价-折扣券优惠金额
        return $this->coupon->getOrderGoodsInScope()->getFinalPrice() - $this->coupon->getOrderGoodsInScope()->getCouponDiscountPrice();
    }

    /**
     * 单件商品当前成交价
     * @param $orderGoods
     * @return mixed
     */
    private function getOrderGoodsPrice(PreGeneratedOrderGoods $orderGoods)
    {
        //之前的
        return $orderGoods->getFinalPrice() - $orderGoods->couponDiscountPrice;
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
        $this->coupon->getOrderGoodsInScope()->getOrderGoodsGroup()->map(function($orderGoods){
                /**
                 * @var $orderGoods PreGeneratedOrderGoods
                 */
                //(优惠券金额/折扣优惠券后价格)*折扣优惠券后价格
//            dd($this->getPrice());
//            dd($this->getOrderGoodsGroupPrice());
//            dd($this->getOrderGoodsPrice($orderGoods));
//            exit;

                $goodsMemberCoupon = new GoodsMemberCoupon();
                $goodsMemberCoupon->amount = $this->getOrderGoodsPrice($orderGoods)/ $this->getOrderGoodsGroupPrice() * $this->getPrice();
                $goodsMemberCoupon->enough =  $this->getOrderGoodsPrice($orderGoods)/ $this->getOrderGoodsGroupPrice() * $this->dbCoupon->enough;
                //todo 需要按照订单方式修改
                if(!isset($orderGoods->coupons)){
                    $orderGoods->coupons = collect();
                }
                $orderGoods->coupons->push($goodsMemberCoupon);

                //$orderGoods->setRelation('coupon',$goodsMemberCoupon);
            });

    }

}