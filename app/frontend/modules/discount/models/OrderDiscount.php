<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/15
 * Time: 下午4:29
 */

namespace app\frontend\modules\discount\models;

use app\common\events\discount\OnDeductionPriceCalculatedEvent;
use app\common\models\Coupon;
use app\frontend\models\order\PreOrderDeduction;
use app\frontend\models\order\PreOrderDiscount;
use app\frontend\modules\coupon\services\CouponService;
use app\frontend\modules\order\models\PreGeneratedOrder;

class OrderDiscount
{
    protected $order;
    private $couponPrice;
    private $deductionPrice;
    public $orderDeductions;
    public $orderCoupons;

    public function __construct(PreGeneratedOrder $order)
    {
        $this->order = $order;
        // 订单抵扣使用记录集合
        $this->orderDeductions = (new PreOrderDeduction())->newCollection();
        $order->setRelation('orderDeductions', $this->orderDeductions);
        // 订单优惠使用记录集合
        $this->orderCoupons = (new PreOrderDiscount())->newCollection();
        $order->setRelation('orderCoupons', $this->orderCoupons);
    }


    /**
     * 获取订单抵扣金额
     * @return mixed
     */
    public function getDeductionPrice()
    {
        if (isset($this->deductionPrice)) {
            return $this->deductionPrice;
        }

        $this->deductionPrice = $this->_getDeductionPrice();

        return $this->deductionPrice;
    }

    private function _getDeductionPrice()
    {
        $event = new OnDeductionPriceCalculatedEvent($this->order);
        event($event);
        return max($this->orderDeductions->sum('amount'),0);
    }

    /**
     * 获取订单优惠金额
     * @return int
     */

    public function getDiscountAmount()
    {
        return $this->getCouponAmount();
    }

    public function getCouponAmount()
    {

        if (isset($this->couponPrice)) {
            return $this->couponPrice;
        }

        $this->couponPrice = $this->_getCouponAmount();

        return $this->couponPrice;
    }

    private function _getCouponAmount()
    {
        $discountCouponService = (new CouponService($this->order, Coupon::COUPON_DISCOUNT));
        $discountPrice = $discountCouponService->getOrderDiscountPrice();
        $discountCouponService->activate();
        //dd($discountPrice);

        $moneyOffCouponService = (new CouponService($this->order, Coupon::COUPON_MONEY_OFF));
        $moneyOffPrice = $moneyOffCouponService->getOrderDiscountPrice();
        //dd($moneyOffPrice);
        $moneyOffCouponService->activate();

        return $discountPrice + $moneyOffPrice;
    }

}