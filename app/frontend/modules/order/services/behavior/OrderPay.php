<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/2/28
 * Time: 上午10:35
 * comment:订单支付类
 */

namespace app\frontend\modules\order\services\behavior;

use app\common\models\Order;

class OrderPay extends OrderOperation
{
    protected $status_before_change = [ORDER::WAIT_PAY];
    protected $status_after_changed = ORDER::WAIT_SEND;
    protected $name = '支付';
    protected $past_tense_class_name = 'OrderPaid';
}