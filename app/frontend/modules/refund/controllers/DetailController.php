<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/13
 * Time: 下午2:00
 */

namespace app\frontend\modules\refund\controllers;

use app\common\components\ApiController;
use app\common\exceptions\AppException;
use app\common\models\refund\RefundApply;

class DetailController extends ApiController
{
    public function index(\Request $request){
        $this->validate($request, [
            'refund_id' => 'required|integer',
        ]);
        $refundApply = RefundApply::find($request->query('refund_id'));
        if(!isset($refundApply)){
            throw new AppException('未找到该退款申请');
        }
        $this->successJson('成功',$refundApply);
    }
}