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

class RefundSend extends ChangeStatusOperation
{
    protected $statusBeforeChange = [self::WAIT_SEND];
    protected $statusAfterChanged = self::WAIT_RECEIVE;
    protected $name = '发货';
    protected $timeField = 'send_time';

    protected $past_tense_class_name = 'OrderSent';

    protected function updateTable()
    {
        $data = Request::only(['refund_id', 'express_sn']);
        $refundExpress = new RefundExpress($data);
        //$data = Request::only(['refund_id', 'express_code', 'express_sn', 'express_company_name']);
        $refundExpress->express_company_name = Request::get('express_company.name');
        $refundExpress->express_code = Request::get('express_company.code');
        $refundExpress->save();
        parent::updateTable();
    }
}