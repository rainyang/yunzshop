<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/25
 * Time: 下午5:14
 */

namespace app\frontend\modules\coupon\services\models;


use app\frontend\modules\goods\services\models\PreGeneratedOrderGoodsModelGroup;
use app\frontend\modules\order\services\models\PreGeneratedOrderModel;

abstract class Coupon
{
    protected $_DbCoupon;
    protected $_OrderModel;
    protected $_OrderGoodsGroup;


    abstract public function valid();
    public function activate(){
        $this->setOrderGoodsDiscountPrice();
    }
    abstract public function getOrderGoodsOfUsedCoupon();
    protected function setOrderGoodsGroup()
    {
        $this->_OrderGoodsGroup = new PreGeneratedOrderGoodsModelGroup($this->getOrderGoodsOfUsedCoupon());
    }
    abstract public function destroy();

    abstract public function getPrice();

    //todo 此处比较混乱
    private function setOrderGoodsDiscountPrice()
    {
        foreach ($this->_OrderGoodsGroup->getOrderGoodsGroup() as $OrderGoods){
            //(优惠券金额/订单商品总金额)*订单商品价格
            $OrderGoods->coupon_discount_price = number_format(($this->getPrice() / $this->_OrderGoodsGroup->getPrice()) * $OrderGoods->getPrice(),2);

        }

    }
}