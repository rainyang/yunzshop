<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/9
 * Time: 下午1:53
 */

namespace app\common\events;


class OrderGoodsDiscountWasCalculated extends Event
{
    private $_order_goods_model;

    public function __construct($order_goods_model)
    {
        $this->_order_goods_model = $order_goods_model;
    }
    public function getOrderGoodsModel(){
        return $this->_order_goods_model;
    }
}