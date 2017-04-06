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
use app\frontend\modules\order\services\models\PreGeneratedOrderModel;

abstract class CreatedOrderEvent extends Event
{
    protected $orderModel;
    protected $order;
    /**
     * AfterOrderReceivedEvent constructor.
     * @param Order $order_model
     */
    public function __construct(PreGeneratedOrderModel $order)
    {
        //$order_model = Order::find($order_id);
        $this->orderModel = $order;
        $this->order = $order->getOrder();
    }
    /**
     * (监听者)获取订单model
     * @return mixed
     */
    public function getOrderModel(){
        return $this->orderModel;
    }
    public function getOrder(){
        return $this->order;
    }

}