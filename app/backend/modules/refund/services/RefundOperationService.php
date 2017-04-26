<?php

namespace app\backend\modules\refund\services;

use app\backend\modules\refund\models\RefundApply;
use app\common\exceptions\AdminException;
use app\common\exceptions\AppException;
use app\frontend\modules\order\services\OrderService;
use app\frontend\modules\refund\services\operation\RefundPass;
use app\frontend\modules\refund\services\operation\RefundSend;

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/13
 * Time: 下午2:21
 */
class RefundOperationService
{
    public static function refundPass()
    {
        $refundSend = RefundPass::find(\Request::input('refund_id'));
        if (!$refundSend) {
            throw new AppException('售后申请记录不存在');
        }
        return $refundSend->execute();

    }

    public static function refundComplete($params)
    {
        $refundApply = RefundApply::where('order_id', $params['order_id'])->first();

        if (!isset($refundApply)) {
            throw new AdminException('(ID:'.$params['order_id'].')退款申请不存在');
        }
        $refundApply->refundMoney();
        OrderService::orderClose(['order_id' => $refundApply->order->id]);
        return true;
    }
}