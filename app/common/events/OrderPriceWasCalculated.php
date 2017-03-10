<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/9
 * Time: 下午1:53
 */

namespace app\common\events;


class OrderPriceWasCalculated extends Event
{
    private $_order_model;

    public function __construct($order_model)
    {
        $this->_order_model = $order_model;
    }
    public function getOrderModel(){
        return $this->_order_model;
    }
}