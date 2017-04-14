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

class RefundCancel extends ChangeStatusOperation
{
    protected $statusBeforeChange = [self::WAIT_CHECK];
    protected $statusAfterChanged = self::CANCEL;
    protected $name = '取消';
    protected $timeField = 'send_time';

    protected $past_tense_class_name = 'OrderSent';

    protected function updateTable()
    {
        parent::updateTable();
    }
}