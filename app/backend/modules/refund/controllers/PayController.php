<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/21
 * Time: 下午6:16
 */

namespace app\backend\modules\refund\controllers;

use app\backend\modules\refund\models\RefundApply;
use app\common\components\BaseController;
use app\common\exceptions\AdminException;
use app\common\exceptions\AppException;
use app\common\services\PayFactory;
use app\frontend\modules\order\services\OrderService;

class PayController extends BaseController
{
    /**
     * 退款
     * @param \Request $request
     */
    public function index(\Request $request)
    {
        $this->validate($request,[
            'refund_id'=>'required'
        ]);
        //dd($request->query('refund_id'));
        //exit;
        /**
         * @var $refundApply RefundApply
         */
        $refundApply = RefundApply::find($request->query('refund_id'));
        if(!isset($refundApply)){
            throw new AppException('未找到退款记录');
        }
        if($refundApply->status != RefundApply::WAIT_REFUND){
            throw new AdminException($refundApply->status_name.'的退款申请,无法执行'.'打款'.'操作');
        }
        //dd($refundApply->order);
        //exit;
        $pay = PayFactory::create(PayFactory::PAY_WEACHAT);

        $result = $pay->doRefund($refundApply->order->order_sn,  $refundApply->order->price, $refundApply->order->price);

        if(!$result){
            $this->error('操作失败');
        }
        $refundApply->refundMoney();
        OrderService::orderClose(['order_id'=>$refundApply->order->id]);
        $this->message('操作成功');

    }
}