<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/15
 * Time: 下午4:29
 */

namespace app\frontend\modules\order\services\models;

use app\common\events\order\OrderDiscountWasCalculated;
class OrderDiscount
{
    private $_order_model;
    private $_discount_details = [];

    public function __construct(PreGeneratedOrderModel $order_model)
    {
        $this->_order_model = $order_model;
        $Event = new OrderDiscountWasCalculated($this);
        event($Event);
        $this->_discount_details = $Event->getData();
    }

    // 获取商品可选的优惠
    public function getDiscountTypes()
    {
        $data[] = [
            'id' => 1,
            'name' => '积分抵扣',
            'max_value' => 20,
            'max_price' => 20,
            'plugin' => 0
        ];
        return $data;
    }

    //提供给订单 累加所有监听者提供的优惠
    public function getDiscountPrice()
    {
        return $result = array_sum(array_column($this->_discount_details, 'price'));
    }

    //提供给监听者 获取订单model
    public function getOrderModel()
    {
        return $this->_order_model;
    }

    //提供给监听者 添加一种优惠
    public function addDiscountDetail($discount_detail)
    {
        $this->_discount_details[] = $discount_detail;
    }

    //提供给订单 保存订单的优惠信息
    public function saveDiscountDetail($order_model)
    {
        //更新订单信息
        $order_model->discount_details = $this->getDiscountDetails();
        $order_model->save();
    }

    //返回运费详情
    private function getDiscountDetails()
    {
        return $this->_discount_details;
    }
}