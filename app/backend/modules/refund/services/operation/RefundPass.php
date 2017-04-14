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
    protected $timeField = 'send_time';

    protected $past_tense_class_name = 'OrderSent';

    protected function updateTable()
    {
        $data = Request::only(['refund_id', 'express_code', 'express_sn', 'express_company_name']);
        $db_express_model = new RefundExpress($data);

        $db_express_model->save();
        parent::updateTable();
    }
}