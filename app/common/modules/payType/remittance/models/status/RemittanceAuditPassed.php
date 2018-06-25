<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/20
 * Time: 上午9:50
 */

namespace app\common\modules\payType\remittance\models\status;

use app\common\models\OrderPay;
use app\common\models\PayType;
use app\common\modules\payType\remittance\models\process\RemittanceAuditProcess;
use app\common\modules\status\StatusObserver;

class RemittanceAuditPassed extends StatusObserver
{
    /**
     * @throws \Exception
     * @throws \app\common\exceptions\AppException
     */
    public function onCreated()
    {
        /**
         * @var RemittanceAuditProcess $process
         */
        $process = RemittanceAuditProcess::find($this->status->model_id);
        // 转账流程->下一步

        $process->remittanceRecord->orderPay->currentProcess()->toNextStatus();
        // 支付记录->支付
        $process->remittanceRecord->orderPay->pay(PayType::REMITTANCE);

    }

}