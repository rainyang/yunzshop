<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/9
 * Time: 下午1:53
 */

namespace app\common\events\order;

use app\common\events\Event;
use app\frontend\modules\order\services\models\OrderDispatch;

class OrderDispatchWasCalculated extends Event
{
    private $_order_dispatch_obj;

    public function __construct(OrderDispatch $order_dispatch_obj)
    {
        $this->_order_dispatch_obj = $order_dispatch_obj;
    }
    //todo
    public function getOrderModel(){
        return $this->_order_dispatch_obj->getOrderModel();
    }
    //提供给监听者 向运费类添加一条运费
    public function addDispatchDetail($dispatch_detail){
        $this->_order_dispatch_obj->addDispatchDetail($dispatch_detail);
    }
}