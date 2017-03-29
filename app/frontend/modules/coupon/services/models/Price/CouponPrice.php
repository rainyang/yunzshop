<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/25
 * Time: 下午5:14
 */

namespace app\frontend\modules\coupon\services\models\Price;


use app\frontend\modules\coupon\services\models\Coupon;
use app\frontend\modules\goods\services\models\PreGeneratedOrderGoodsModelGroup;
use app\frontend\modules\order\services\models\PreGeneratedOrderModel;

abstract class CouponPrice
{
    /**
     * 优惠券数据库model
     * @var
     */
    protected $dbCoupon;
    /**
     * @var Coupon
     */
    protected $coupon;
    /**
     * @var PreGeneratedOrderModel
     */
    protected $orderModel;
    /**
     * @var PreGeneratedOrderGoodsModelGroup
     */
    protected $_OrderGoodsGroup;

    public function __construct(Coupon $coupon)
    {
        $this->coupon = $coupon;
        $this->dbCoupon = $coupon->getMemberCoupon()->belongsToCoupon;
        $this->orderModel = $coupon->getPreGeneratedOrderModel();

    }
    abstract public function valid();
    /**
     * 订单获取优惠券 金额
     * @return mixed
     */
    abstract public function getPrice();
    abstract public function setOrderGoodsDiscountPrice();
}