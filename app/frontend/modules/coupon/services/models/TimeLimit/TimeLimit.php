<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/29
 * Time: 下午5:23
 */

namespace app\frontend\modules\coupon\services\models\TimeLimit;


use app\frontend\modules\coupon\services\models\Coupon;
use app\frontend\modules\goods\services\models\PreGeneratedOrderGoodsModelGroup;
use app\frontend\modules\order\services\models\PreGeneratedOrderModel;

abstract class TimeLimit
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
    protected $orderGoodsModelGroup;

    public function __construct(Coupon $coupon)
    {
        $this->coupon = $coupon;
        $this->dbCoupon = $coupon->getMemberCoupon()->belongsToCoupon;
        $this->orderModel = $coupon->getPreGeneratedOrderModel();

    }
    abstract public function valid();
}