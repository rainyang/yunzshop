<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/17
 * Time: 上午9:36
 */

namespace app\common\events\order;


use app\common\events\Event;
use app\common\models\Order;

abstract class OrderEvent extends Event
{
    protected $_order_model;
    /**
     * AfterOrderReceivedEvent constructor.
     * @param Order $order_model
     */
    public function __construct(Order $order_model)
    {
        //$order_model = Order::find($order_id);
        $this->_order_model = $order_model;
    }
    /**
     * (监听者)获取订单model
     * @return mixed
     */
    public function getOrderModel(){
        return $this->_order_model;
    }
}