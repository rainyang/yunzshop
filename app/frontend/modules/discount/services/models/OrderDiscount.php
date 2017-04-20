<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/15
 * Time: 下午4:29
 */

namespace app\frontend\modules\discount\services\models;

use app\common\events\discount\OnCouponPriceCalculatedEvent;
use app\common\events\discount\OnDeductionPriceCalculatedEvent;
use app\common\models\Coupon;
use app\frontend\modules\coupon\services\TestService;
use app\frontend\modules\order\services\models\PreGeneratedOrderModel;

class OrderDiscount extends Discount
{
    protected $order;
    private $couponPrice;
    private $deductionPrice;

    public function __construct(PreGeneratedOrderModel $order)
    {
        $this->order = $order;
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
        $Event = new OnDeductionPriceCalculatedEvent($this->order);
        $data = $Event->getData();
        return max(array_sum(array_column($data, 'price')), 0);
    }

    /**
     * 获取订单优惠金额
     * @return int
     */

    public function getDiscountPrice()
    {
        return $this->getCouponPrice();
    }

    public function getCouponPrice()
    {

        if (isset($this->couponPrice)) {
            return $this->couponPrice;
        }

        $this->couponPrice = $this->_getCouponPrice();

        return $this->couponPrice;
    }

    private function _getCouponPrice()
    {

        $discountPrice = (new TestService($this->order, Coupon::COUPON_DISCOUNT))->getOrderDiscountPrice();
        //dd($discountPrice);
        $moneyOffPrice = (new TestService($this->order, Coupon::COUPON_MONEY_OFF))->getOrderDiscountPrice();
        //dd($moneyOffPrice);

        return $discountPrice + $moneyOffPrice;
    }
}