<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/20
 * Time: 上午10:57
 */

namespace app\frontend\modules\discount\services\models;


abstract class Discount
{
    /**
     * OrderDiscount constructor.
     * @param array $discount_details
     */
    public function __construct( )
    {
    }
    /**
     * 改价时 添加一种优惠
     * @param $discount_detail
     */
    public function addDiscountDetail($discount_detail)
    {
        $this->_discount_details[] = $discount_detail;
    }

    /**
     * 提供给订单(插入到表中) 返回运费详情
     * @return array
     */
    public function getDiscountDetails()
    {
        return $this->_discount_details;
    }
    /**
     * 提供给订单 累加所有监听者提供的优惠
     * @return number
     */
    public function getDiscountPrice()
    {
        return $result = array_sum(array_column($this->_discount_details, 'price'));
    }
}