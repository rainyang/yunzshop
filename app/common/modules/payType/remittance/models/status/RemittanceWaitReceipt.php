<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/20
 * Time: 上午9:50
 */

namespace app\common\modules\payType\remittance\models\status;

use app\common\models\Order;
use app\common\models\PayType;
use app\common\modules\payType\remittance\models\flows\RemittanceAuditFlow;
use app\common\modules\status\StatusObserver;
use app\frontend\modules\member\services\MemberService;
use app\frontend\modules\payType\remittance\PreRemittanceRecord;
use app\frontend\modules\payType\remittance\process\RemittanceProcess;

class RemittanceWaitReceipt extends StatusObserver
{


    /**
     * @throws \Exception
     * @throws \app\common\exceptions\AppException
     */
    public function onCreated()
    {

        /**
         * @var RemittanceProcess $process
         */
        $process = RemittanceProcess::find($this->status->model_id);
        $process->orderPay->orders->each(function (Order $order) use($process) {
            $order->pay_type_id = PayType::REMITTANCE;
            $order->order_pay_id = $process->orderPay->id;
            $order->save();
        });
        // todo 从参数中获取  验证参数是否存在
        $transferRecord = new PreRemittanceRecord(
            [
                'report_url' => request()->input('report_url',''),
                'note' => request()->input('note',''),
                'uid' => MemberService::getCurrentMemberModel()->uid,
                'order_pay_id' => $process->model_id,
                'card_no' => request()->input('card_no',''),
                'amount' => request()->input('amount',0),
                'bank_name' => request()->input('bank_name',''),
            ]
        );
        $transferRecord->save();

        $transferRecord->addProcess(RemittanceAuditFlow::first());

    }

}