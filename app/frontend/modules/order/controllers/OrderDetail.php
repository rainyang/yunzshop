<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/3/1
 * Time: 下午5:41
 */

namespace app\frontend\modules\order\controllers;

use app\frontend\modules\order\model\behavior;
use app\frontend\modules\order\service;

class OrderDetail
{
    private $order_id;
    private $to;

    function __construct()
    {
        $this->order_id = \YunShop::request()->id;
        $this->to = trim(\YunShop::request()->to);
    }

    function orderDetail()
    {
        $order = behavior\Order::getDbOrder($this->order_id);
        service\OrderEmpty::isEmpty($order);
        //$shopset = m("common")->getSysset("shop");
        switch ($this->to)
        {
            case 'refund':
                service\OrderRefundService::orderRefund($order);
                break;
        }
    }
}