<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/2/28
 * Time: 上午11:19
 * comment: 取消支付
 */

namespace app\frontend\modules\order\services\behavior;


use app\common\models\Order;

class OrderCancelPay extends ChangeStatusOperation
{
    protected $status_before_change = [Order::WAIT_SEND];
    protected $status_after_changed = 0;
    protected $name = '取消支付';
    protected $past_tense_class_name = 'OrderCancelPaid';
}