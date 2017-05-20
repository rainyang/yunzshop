<?php

namespace app\common\events\payment;

use app\common\events\Event;

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/5/17
 * Time: 下午5:48
 */
class GetOrderPaymentTypeEvent extends Event
{
    private $orders;

    function __construct($orders)
    {
        $this->orders = $orders;
    }
    public function getOrders(){
        return $this->orders;
    }
}