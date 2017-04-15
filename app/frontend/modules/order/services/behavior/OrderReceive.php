<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/2/28
 * Time: 上午10:57
 * comment:订单收货类
 */

namespace app\frontend\modules\order\services\behavior;

use app\common\events\order\AfterOrderReceivedEvent;
use app\common\models\Order;


class OrderReceive extends ChangeStatusOperation
{
    protected $status_before_change = [ORDER::WAIT_RECEIVE];
    protected $statusAfterChanged = ORDER::COMPLETE;
    protected $name = '收货';
    protected $time_field = 'finish_time';
    protected $past_tense_class_name = 'OrderReceived';
}