<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/3/3
 * Time: 下午3:43
 */

namespace app\frontend\modules\refund\services\operation;

use app\common\models\refund\RefundExpress;
use \Request;

class ReceiveResendGoods extends ChangeStatusOperation
{
    protected $statusBeforeChange = [self::WAIT_RECEIVE_RESEND_GOODS];
    protected $statusAfterChanged = self::COMPLETE;
    protected $name = '收货';
    //protected $timeField = 'send_time';

    protected $pastTenseClassName = '';

    protected function updateTable()
    {
        parent::updateTable();
    }
}