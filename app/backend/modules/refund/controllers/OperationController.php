<?php
namespace app\backend\modules\refund\controllers;

use app\backend\modules\refund\services\RefundOperationService;
use app\common\components\BaseController;
use app\common\models\refund\RefundApply;

/**
 * 退款申请操作
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/13
 * Time: 下午3:05
 */
class OperationController extends BaseController
{
    public function entrance(\Request $request)
    {
        $this->validate($request, [
            'refund_id' => 'required|filled|integer'
        ]);

        switch ($request->refundstatus) {
            case '-1':
                //驳回

                break;
            case '1':
                //通过
                $this-> pass();
                break;
            default:
                //手动打款
                $this-> manual();

        }
        
        

    }

    public function pass()
    {
        
        
        if(RefundOperationService::refundPass()){
            return $this->message('操作成功');
        }
        return $this->message('操作失败','','error');

    }

    public function  manual()
    {
        if(RefundOperationService::refundPass()){
            return $this->message('操作成功');
        }
        return $this->message('操作失败','','error');

    }
}