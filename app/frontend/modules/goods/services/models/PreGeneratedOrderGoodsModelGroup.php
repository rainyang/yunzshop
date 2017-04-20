<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/28
 * Time: 下午1:44
 */

namespace app\frontend\modules\goods\services\models;

use Illuminate\Support\Collection;

class PreGeneratedOrderGoodsModelGroup
{
    private $orderGoodsGroup;

    public function __construct(Collection $OrderGoodsGroup)
    {
        $this->orderGoodsGroup = $OrderGoodsGroup;
    }

    /**
     * 获取商城价
     * @return int
     */
    public function getPrice()
    {
        return $this->orderGoodsGroup->sum(function ($orderGoods) {
            return $orderGoods->getPrice();
        });
    }

    /**
     * 获取销售价
     * @return int
     */
    public function getVipPrice()
    {
        $result = 0;
        foreach ($this->orderGoodsGroup as $OrderGoods) {
            /**
             * @var $OrderGoods PreGeneratedOrderGoodsModel
             */
            $result += $OrderGoods->getVipPrice();
        }
        return $result;
    }

    /**
     * 获取折扣优惠券优惠金额
     * @return int
     */
    public function getCouponDiscountPrice()
    {
        $result = 0;
        foreach ($this->orderGoodsGroup as $OrderGoods) {
            /**
             * @var $OrderGoods PreGeneratedOrderGoodsModel
             */
            $result += $OrderGoods->couponDiscountPrice;
        }
        return $result;
    }

    public function getOrderGoodsGroup()
    {
        return $this->orderGoodsGroup;
    }
}