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

    // todo 获取订单抵扣金额
    public function getDeductionPrice()
    {
        $Event = new OnDeductionPriceCalculatedEvent();
        $data = $Event->getData();
        $price = max(array_sum(array_column($data, 'price')), 0);
        return $price;
    }

    //todo 获取订单优惠金额

    public function getDiscountPrice()
    {
        return $this->getCouponPrice() + $this->getMemberLevelDiscountPrice();
    }

    public function getMemberLevelDiscountPrice()
    {
        $Event = new OnCouponPriceCalculatedEvent();
        $data = $Event->getData();
        $price = max(array_sum(array_column($data, 'price')), 0);
        return $price;
    }

    public function getCouponPrice()
    {
        //todo 对商品价格进行处理
        $obj = new TestService($this->_Order);

        return - $obj->getOrderDiscountPrice();
    }
}