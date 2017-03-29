<?php
/**
 * 折扣优惠券
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/25
 * Time: 下午5:20
 */

namespace app\frontend\modules\coupon\services\models\Price;


use app\frontend\modules\coupon\services\models\Coupon;
use app\frontend\modules\goods\services\models\PreGeneratedOrderGoodsModel;
use app\frontend\modules\goods\services\models\PreGeneratedOrderGoodsModelGroup;
use app\frontend\modules\order\services\models\PreGeneratedOrderModel;

class DiscountCouponPrice extends CouponPrice
{

    public function valid(){
        return true;
    }
    public function getPrice()
    {
        return (1 - $this->dbCoupon->discount) * $this->coupon->getOrderGoodsInScope()->getVipPrice();
    }
    /**
     * 分配优惠金额 ,折扣商品使用 销售价计算
     */
    public function setOrderGoodsDiscountPrice()
    {
        //echo 1;exit;
        //dd($this->getOrderGoodsInScope());
        foreach ($this->coupon->getOrderGoodsInScope()->getOrderGoodsGroup() as $OrderGoods) {
            /**
             * @var $OrderGoods PreGeneratedOrderGoodsModel
             */
            //(优惠券金额/订单商品总金额)*订单商品价格
            //dd(number_format(-($this->getDiscountPrice() / $this->getOrderGoodsInScope()->getPrice()) * $OrderGoods->getPrice(), 2));exit;
            $OrderGoods->coupon_discount_price += number_format(($this->getPrice() / $this->coupon->getOrderGoodsInScope()->getVipPrice()) * $OrderGoods->getVipPrice(), 2);

        }
    }
}