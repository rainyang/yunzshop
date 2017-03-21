<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/3/3
 * Time: 下午3:43
 */

namespace app\frontend\modules\order\services\behavior;

use app\common\models\Order;

class OrderSend extends ChangeStatusOperation
{
    protected $status_before_change = [ORDER::WAIT_SEND];
    protected $status_after_changed = ORDER::WAIT_RECEIVE;
    protected $name = '发货';
    protected $past_tense_class_name = 'OrderSent';
}