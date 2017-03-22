<?php

namespace app\frontend\modules\order\services\behavior;

/*
 * 取消发货
 */
use app\common\models\Order;

class OrderCancelSend extends ChangeStatusOperation
{
    protected $status_before_change = [Order::WAIT_RECEIVE];
    protected $status_after_changed = Order::WAIT_SEND;
    protected $name = '取消发货';
    protected $past_tense_class_name = 'OrderCancelSent';


}