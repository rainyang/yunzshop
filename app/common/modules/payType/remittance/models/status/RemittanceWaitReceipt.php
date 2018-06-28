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
use app\common\models\Process;
use app\common\modules\payType\remittance\models\flows\RemittanceAuditFlow;
use app\common\modules\payType\remittance\models\process\RemittanceAuditProcess;
use app\common\modules\status\StatusObserver;
use app\frontend\modules\member\services\MemberService;
use app\frontend\modules\payType\remittance\PreRemittanceRecord;
use app\frontend\modules\payType\remittance\process\RemittanceProcess;

class RemittanceWaitReceipt
{


    /**
     * @param Process $process
     * @throws \Exception
     * @throws \app\common\exceptions\AppException
     */
    public function handle(Process $process)
    {
        $process = RemittanceProcess::find($process->id);
        /**
         * @var RemittanceProcess $process
         */
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
                'uid' => $process->orderPay->uid,
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