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

class RefundPass extends ChangeStatusOperation
{
    protected $statusBeforeChange = [self::WAIT_CHECK];
    protected $statusAfterChanged = self::WAIT_SEND;
    protected $name = '通过';
    protected $timeField = 'operate_time';


    protected function updateTable()
    {
        return parent::updateTable();
    }
}