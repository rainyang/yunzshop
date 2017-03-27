<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/25
 * Time: 下午5:20
 */

namespace app\frontend\modules\coupon\services\models;


use app\frontend\modules\coupon\services\models\Coupon;
use app\frontend\modules\goods\services\models\PreGeneratedOrderGoodsModelGroup;
use app\frontend\modules\order\services\models\PreGeneratedOrderModel;

class DiscountCoupon extends Coupon
{
    public function __construct(PreGeneratedOrderModel $OrderModel, \app\common\models\Coupon $DbCoupon)
    {
        $this->_DbCoupon = $DbCoupon;
        $this->_OrderModel = $OrderModel;

    }
    public function destroy()
    {
        //todo 监听者调用此方法,记录优惠券已使用
    }

    public function getOrderGoodsOfUsedCoupon()
    {

        return $this->_OrderModel->getOrderGoodsModels();
    }
    public function getPrice()
    {
        return $this->_DbCoupon->deduct;

    }
    public function valid()
    {
        // todo 判断订单是否满足 优惠券使用条件
        $this->_OrderGoodsGroup = new PreGeneratedOrderGoodsModelGroup($this->getOrderGoodsOfUsedCoupon());

        return true;
    }

}