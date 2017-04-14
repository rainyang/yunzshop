<?php

/**
 * 退款申请操作
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/13
 * Time: 下午3:05
 */
class OperationController
{
    public function pass(\Request $request)
    {
        $this->validate($request, [
            'refund_id' => 'required|filled|integer'
        ]);
        RefundOperationService::refundPass();

    }
}