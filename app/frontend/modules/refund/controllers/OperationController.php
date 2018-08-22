<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/13
 * Time: 下午2:17
 */

namespace app\frontend\modules\refund\controllers;


use app\common\components\ApiController;
use app\common\modules\refund\services\RefundService;
use app\frontend\modules\refund\services\RefundOperationService;

class OperationController extends ApiController
{
    public $transactionActions = ['*'];
    public function send(\Request $request)
    {
        $this->validate([
            'refund_id' => 'required|filled|integer',
            'express_company_code' => 'required|string',
            'express_company_name' => 'required|string',
            'express_sn' => 'required|filled|string',
        ]);
        RefundOperationService::refundSend();
        return $this->successJson();
    }

    /**
     * 确认收货
     * @param \Request $request
     * @return mixed
     */
    public function complete(\Request $request)
    {
        $this->validate([
            'refund_id' => 'required'
        ]);
        /**
         * @var $this ->refundApply RefundApply
         */
        (new RefundService())->pay($request['refund_id']);
        return $this->successJson();

    }

    public function cancel(\Request $request)
    {
        $this->validate([
            'refund_id' => 'required|filled|integer',
        ]);
        RefundOperationService::refundCancel();
        return $this->successJson();

    }

}