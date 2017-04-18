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
        dd($request->toArray());
//        switch ($request) {
//            case 'first_level':
//                $hierarchy = '1';//分销层级
//                break;
//            case 'second_level':
//                $hierarchy = '2';//分销层级
//                break;
//            default:
//                $hierarchy = '3';//分销层级
//        }
        
        
        $this->pass();
    }

    public function pass()
    {
        
        if(RefundOperationService::refundPass()){
            return $this->message('操作成功');
        }
        return $this->message('操作失败','','error');

    }
}