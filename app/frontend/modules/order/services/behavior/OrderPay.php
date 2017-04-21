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

class OrderPay extends ChangeStatusOperation
{
    protected $statusBeforeChange = [ORDER::WAIT_PAY];
    protected $statusAfterChanged = ORDER::WAIT_SEND;
    protected $name = '支付';
    protected $time_field = 'pay_time';
    protected $past_tense_class_name = 'OrderPaid';

}