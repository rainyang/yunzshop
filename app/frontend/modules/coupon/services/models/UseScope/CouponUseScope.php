<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/28
 * Time: 下午3:00
 */

namespace app\frontend\modules\coupon\services\models\UseScope;


use app\frontend\modules\coupon\services\models\Coupon;
use app\frontend\modules\goods\services\models\PreGeneratedOrderGoodsModelGroup;

abstract class CouponUseScope
{
    /**
     * @var Coupon
     */
    protected $coupon;
    /**
     * @var PreGeneratedOrderGoodsModelGroup
     */
    protected $orderGoodsGroup;
    public function __construct(Coupon $coupon)
    {
        $this->coupon = $coupon;
    }

    public function getOrderGoodsInScope(){
        return $this->orderGoodsGroup;
    }
}