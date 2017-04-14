<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/13
 * Time: 下午2:17
 */

namespace app\frontend\modules\refund\controllers;


use app\common\components\ApiController;
use app\frontend\modules\refund\services\RefundOperationService;

class OperationController extends ApiController
{
    public function send(\Request $request){
        $this->validate($request,[
            'refund_id' => 'required|filled|integer',
            'express_code' => 'required|filled|string',
            'express_sn' => 'required|filled|string',
            'express_company_name' => 'required|filled|string',
        ]);
        RefundOperationService::refundSend();
        return $this->successJson();
    }
    public function cancel(\Request $request){
        $this->validate($request,[
            'refund_id' => 'required|filled|integer',
        ]);
        RefundOperationService::cancel();
        return $this->successJson();

    }

}