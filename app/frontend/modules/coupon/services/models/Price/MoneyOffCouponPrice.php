<?php
/**
 * 立减优惠券
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/25
 * Time: 下午5:21
 */

namespace app\frontend\modules\coupon\services\models\Price;

use app\common\models\coupon\GoodsMemberCoupon;
use app\frontend\modules\goods\services\models\PreGeneratedOrderGoodsModel;

class MoneyOffCouponPrice extends CouponPrice
{

    /**
     * 累加所有商品会员价
     * @return int
     */
    protected function getOrderGoodsGroupPrice()
    {
        //会员价-折扣券优惠金额
        return $this->coupon->getOrderGoodsInScope()->getVipPrice() - $this->coupon->getOrderGoodsInScope()->getCouponDiscountPrice();
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
        $this->coupon->getOrderGoodsInScope()->getOrderGoodsGroup()->map(function($orderGoods){
                /**
                 * @var $orderGoods PreGeneratedOrderGoodsModel
                 */
                //(优惠券金额/折扣优惠券后价格)*折扣优惠券后价格
//            dd($this->getPrice());
//            dd($this->getOrderGoodsGroupPrice());
//            dd($this->getOrderGoodsPrice($orderGoods));
//            exit;

                $goodsMemberCoupon = new GoodsMemberCoupon();
                $goodsMemberCoupon->amount = number_format(( $this->getOrderGoodsPrice($orderGoods)/ $this->getOrderGoodsGroupPrice()) * $this->getPrice(), 2);
                $goodsMemberCoupon->enough = number_format(( $this->getOrderGoodsPrice($orderGoods)/ $this->getOrderGoodsGroupPrice()) * $this->dbCoupon->enough, 2);
                if(!isset($orderGoods->coupons)){
                    $orderGoods->coupons = collect();
                }
                $orderGoods->coupons->push($goodsMemberCoupon);

                //$orderGoods->setRelation('coupon',$goodsMemberCoupon);
            });

    }

}