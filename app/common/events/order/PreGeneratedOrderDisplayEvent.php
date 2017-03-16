<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/14
 * Time: 下午1:38
 */

namespace app\common\events\order;
use app\common\events\Event;


class PreGeneratedOrderDisplayEvent extends Event
{
    private $_order_model;
    private $_order_controller;

    public function __construct($order_controller,$order_model)
    {
        $this->_order_controller = $order_controller;
        $this->_order_model = $order_model;
    }

    public function getOrderModel()
    {
        return $this->_order_model;
    }

    public function addDispatchInfo($dispatch_data)
    {
        $this->_order_controller->addData('Optional_dispatch_data',$dispatch_data);
    }
}