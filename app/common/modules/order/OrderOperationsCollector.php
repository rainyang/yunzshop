<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/8/2
 * Time: 下午2:01
 */

namespace app\common\models\order;


use app\common\models\Order;

class OrderOperationsCollector
{
    private $contract = [];

    public function __construct()
    {
        $this->loadContract();
    }

    private function loadContract()
    {
        $this->contract = config('shop-foundation.order.member_order_operations');
    }


    public function getOperations(Order $order)
    {
        $this->contract[$order->status];
    }
}