<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/20
 * Time: 上午9:50
 */

namespace app\common\modules\payType\remittance\models\status;

use app\common\models\PayType;
use app\common\models\Process;
use app\common\modules\payType\remittance\models\process\RemittanceAuditProcess;

class RemittanceAuditPassed
{
    /**
     * @param Process $process
     * @throws \Exception
     * @throws \app\common\exceptions\AppException
     */
    public function handle(Process $process)
    {
        $process = RemittanceAuditProcess::find($process->id);

        /**
         * @var RemittanceAuditProcess $process
         */
        // 转账流程->下一步
        $process->remittanceRecord->orderPay->currentProcess()->toNextStatus();
        // 支付记录->支付
        $process->remittanceRecord->orderPay->pay(PayType::REMITTANCE);

    }

}