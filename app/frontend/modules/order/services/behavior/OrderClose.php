<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/2/28
 * Time: 上午11:07
 * comment:订单关闭类
 */

namespace app\frontend\modules\order\services\behavior;
use app\common\events\order\AfterOrderCanceledEvent;
use app\common\models\Order;


class OrderClose extends ChangeStatusOperation
{
    protected $status_before_change = [ORDER::WAIT_PAY];
    protected $statusAfterChanged = ORDER::CLOSE;
    protected $name = '关闭';
    protected $time_field = 'cancel_time';
    protected $past_tense_class_name = 'OrderCanceled';
}