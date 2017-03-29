<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/28
 * Time: 下午1:44
 */

namespace app\frontend\modules\goods\services\models;

class PreGeneratedOrderGoodsModelGroup
{
    private $_OrderGoodsGroup;
    public function __construct(array $OrderGoodsGroup)
    {
        $this->_OrderGoodsGroup = $OrderGoodsGroup;
    }

    /**
     * 获取商城价
     * @return int
     */
    public function getPrice(){
        $result = 0;
        foreach ($this->_OrderGoodsGroup as $OrderGoods){
            /**
             * @var $OrderGoods PreGeneratedOrderGoodsModel
             */
            $result += $OrderGoods->getPrice();
        }
        return $result;
    }

    /**
     * 获取销售价
     * @return int
     */
    public function getVipPrice(){
        $result = 0;
        foreach ($this->_OrderGoodsGroup as $OrderGoods){
            /**
             * @var $OrderGoods PreGeneratedOrderGoodsModel
             */
            $result += $OrderGoods->Goods->vip_price;
        }
        return $result;
    }
    /**
     * 获取折扣优惠券优惠金额
     * @return int
     */
    public function getCouponDiscountPrice(){
        $result = 0;
        foreach ($this->_OrderGoodsGroup as $OrderGoods){
            /**
             * @var $OrderGoods PreGeneratedOrderGoodsModel
             */
            $result += $OrderGoods->coupon_discount_price;
        }
        return $result;
    }
    public function getOrderGoodsGroup(){
        return $this->_OrderGoodsGroup;
    }
}