<?php
/**
 * 折扣优惠券
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/25
 * Time: 下午5:20
 */

namespace app\frontend\modules\coupon\services\models\Price;


use app\common\models\coupon\GoodsMemberCoupon;
use app\frontend\modules\goods\services\models\PreGeneratedOrderGoodsModel;

class DiscountCouponPrice extends CouponPrice
{
    public function getPrice()
    {
        return (1 - $this->dbCoupon->discount/10) * $this->coupon->getOrderGoodsInScope()->getFinalPrice();
    }
    protected function getOrderGoodsGroupPrice()
    {
        //会员价-折扣券优惠金额
        return $this->coupon->getOrderGoodsInScope()->getFinalPrice();
    }
    /**
     * 分配优惠金额 ,折扣商品使用 销售价计算
     */
    public function setOrderGoodsDiscountPrice()
    {
        //echo 1;exit;
        //dd($this->coupon);
        $this->coupon->getOrderGoodsInScope()->getOrderGoodsGroup()->map(function($orderGoods){
            /**
             * @var $OrderGoods PreGeneratedOrderGoodsModel
             */
            //(优惠券金额/订单商品总金额)*订单商品价格
            //dd(number_format(-($this->getDiscountPrice() / $this->getOrderGoodsInScope()->getPrice()) * $OrderGoods->getPrice(), 2));exit;
            $goodsMemberCoupon = new GoodsMemberCoupon();
            //todo 需要按照订单方式修改

            $goodsMemberCoupon->amount = ($orderGoods->getFinalPrice() / $this->coupon->getOrderGoodsInScope()->getFinalPrice()) * $this->getPrice();
            $goodsMemberCoupon->enough = ($orderGoods->getFinalPrice() / $this->coupon->getOrderGoodsInScope()->getFinalPrice()) * $this->dbCoupon->enough;
            if(!isset($orderGoods->coupons)){
                $orderGoods->coupons = collect();
            }
            $orderGoods->coupons->push($goodsMemberCoupon);
        });
    }
}