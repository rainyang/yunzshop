<?php

namespace app\frontend\modules\refund\services;

use app\common\exceptions\AppException;
use app\frontend\modules\refund\services\operation\RefundCancel;
use app\frontend\modules\refund\services\operation\RefundSend;

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/13
 * Time: 下午2:21
 */
class RefundOperationService
{
    public static function refundSend()
    {
        //todo 需要与后台操作统一
        $refundSend = RefundSend::find(\Request::input('refund_id'));
        if (!$refundSend) {
            throw new AppException('售后申请记录不存在');
        }
        $refundSend->enable();
        return $refundSend->execute();

    }

    public static function refundCancel()
    {
        //todo 需要与后台操作统一
        $refundCancel = RefundCancel::find(\Request::query('refund_id'));
        if (!$refundCancel) {
            throw new AppException('售后申请记录不存在');
        }
        $refundCancel->enable();
        return $refundCancel->execute();

    }
}