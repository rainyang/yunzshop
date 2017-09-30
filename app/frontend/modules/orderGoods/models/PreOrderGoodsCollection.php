<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/28
 * Time: 下午1:44
 */

namespace app\frontend\modules\orderGoods\models;


class PreOrderGoodsCollection extends \Illuminate\Database\Eloquent\Collection
{
    private $orderGoodsGroup;


    public function __construct($items = [])
    {
        parent::__construct($items);
    }

    /**
     * 获取商城价
     * @return int
     */
    public function getPrice()
    {
        return $this->sum(function ($orderGoods) {
            return $orderGoods->getPrice();
        });
    }

    /**
     * 获取销售价
     * @return int
     */
    public function getFinalPrice()
    {
        return $this->sum(function ($orderGoods) {
            return $orderGoods->getPrice();
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

    public function getOrderGoodsCollection()
    {
        return $this->orderGoodsGroup;
    }
}