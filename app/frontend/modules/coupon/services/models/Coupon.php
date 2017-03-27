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
    /**
     * 优惠券数据库model
     * @var
     */
    protected $_DbCoupon;
    /**
     * @var PreGeneratedOrderModel
     */
    protected $_OrderModel;
    /**
     * @var PreGeneratedOrderGoodsModelGroup
     */
    protected $_OrderGoodsGroup;

    /**
     * 优惠券可使用
     * @return mixed
     */
    abstract public function valid();

    /**
     * 激活优惠券
     */
    public function activate()
    {
        $this->setOrderGoodsDiscountPrice();
    }

    /**
     * 获取使用了这张优惠券的所有订单商品
     * @return mixed
     */
    abstract public function getOrderGoodsOfUsedCoupon();

    /**
     * 将订单商品装入 订单商品组对象
     */
    protected function setOrderGoodsGroup()
    {
        $this->_OrderGoodsGroup = new PreGeneratedOrderGoodsModelGroup($this->getOrderGoodsOfUsedCoupon());
    }

    /**
     * 订单完成后记录优惠券使用信息
     * @return mixed
     */
    abstract public function destroy();

    /**
     * 订单获取优惠券 金额
     * @return mixed
     */
    abstract public function getPrice();

    /**
     * 设置订单商品优惠金额
     */
    private function setOrderGoodsDiscountPrice()
    {
        foreach ($this->_OrderGoodsGroup->getOrderGoodsGroup() as $OrderGoods) {
            //(优惠券金额/订单商品总金额)*订单商品价格
            $OrderGoods->coupon_discount_price = number_format(-($this->getPrice() / $this->_OrderGoodsGroup->getPrice()) * $OrderGoods->getPrice(), 2);

        }

    }
}