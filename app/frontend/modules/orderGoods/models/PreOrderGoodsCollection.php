<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/28
 * Time: 下午1:44
 */

namespace app\frontend\modules\orderGoods\models;


use app\framework\Database\Eloquent\Collection;

class PreOrderGoodsCollection extends Collection
{
    public function setOrder($order){
        foreach ($this as $orderGoods){
            $orderGoods->setOrder($order);
        }
    }
    /**
     * 获取原价
     * @return int
     */
    public function getGoodsPrice()
    {
        return $this->sum(function (PreOrderGoods $orderGoods) {
            return $orderGoods->getGoodsPrice();
        });
    }

    /**
     * 获取成交价
     * @return int
     */
    public function getPrice()
    {
        return $this->sum(function (PreOrderGoods $orderGoods) {
            return $orderGoods->getPrice();
        });
    }

    /**
     * 获取支付价
     * @return int
     */
    public function getPaymentAmount()
    {
        return $this->sum(function (PreOrderGoods $orderGoods) {
            return $orderGoods->getPaymentAmount();
        });
    }

    /**
     * 获取折扣优惠券优惠金额
     * @return int
     */
    public function getCouponDiscountPrice()
    {
        return $this->sum(function ($orderGoods) {
            return $orderGoods->couponDiscountPrice;
        });
    }

    /**
     * 订单商品集合中包含虚拟物品
     * @return bool
     */
    public function hasVirtual(){
        return $this->contains(function ($aOrderGoods) {
            // 包含虚拟商品
            return $aOrderGoods->goods->type == 2;
        });
    }
}