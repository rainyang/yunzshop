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
use app\frontend\modules\coupon\services\TestService;
use app\frontend\modules\order\services\models\PreGeneratedOrderModel;

class OrderDiscount extends Discount
{
    protected $_Order;
    private $_coupon_price;
    private $_deduction_price;

    public function __construct(PreGeneratedOrderModel $Order)
    {
        //dd($Order);exit;
        $this->_Order = $Order;
    }

    // todo 获取订单可选的抵扣
    public function getDeductions()
    {
        $data[] = [
            'id' => 1,
            'name' => '积分抵扣',
            'value' => 20,
            'price' => 20,
            'plugin' => 0
        ];
        return $data;
    }

    // todo 获取订单可选的优惠券
    public function getCoupons()
    {
        $data[] = [
            'id' => 1,
            'name' => '优惠券1',
            'max_value' => 30,
            'max_price' => 30,
            'plugin' => 0
        ];
        return $data;
    }

    /**
     * 获取订单抵扣金额
     * @return mixed
     */
    public function getDeductionPrice()
    {
        if(isset($this->_deduction_price)){
            return $this->_deduction_price;
        }

        $this->_deduction_price = $this->_getDeductionPrice();

        return $this->_deduction_price;
    }
    private function _getDeductionPrice(){
        $Event = new OnDeductionPriceCalculatedEvent($this->_Order);
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
        if(isset($this->_coupon_price)){
            return $this->_coupon_price;
        }

        $this->_coupon_price = $this->_getCouponPrice();

        return $this->_coupon_price;
    }
    private function _getCouponPrice()
    {
        //todo 对商品价格进行处理
        $obj = new TestService($this->_Order);

        return - $obj->getOrderDiscountPrice();
    }
}