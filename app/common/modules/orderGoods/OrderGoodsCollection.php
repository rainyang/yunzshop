<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/11/26
 * Time: 3:52 PM
 */

namespace app\common\modules\orderGoods\models;

use app\framework\Database\Eloquent\Collection;

class OrderGoodsCollection extends Collection
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