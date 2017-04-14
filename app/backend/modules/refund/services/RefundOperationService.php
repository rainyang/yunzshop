<?php
namespace app\backend\modules\refund\services;

use app\common\exceptions\AppException;
use app\frontend\modules\refund\services\operation\RefundSend;

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/13
 * Time: 下午2:21
 */
class RefundOperationService
{
    public static function refundPass(){
        $refundSend = RefundSend::find(\Request::query('refund_id'));
        if(!$refundSend){
            throw new AppException('售后申请记录不存在');
        }
        $result = $refundSend->execute();
        exit;
    }
}