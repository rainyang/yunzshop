<?php
namespace app\backend\modules\refund\controllers;

use app\backend\modules\refund\services\RefundOperationService;
use app\common\components\BaseController;

/**
 * 退款申请操作
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/13
 * Time: 下午3:05
 */
class OperationController extends BaseController
{
    public function pass(\Request $request)
    {
        $this->validate($request, [
            'refund_id' => 'required|filled|integer'
        ]);
        RefundOperationService::refundPass();

    }
}