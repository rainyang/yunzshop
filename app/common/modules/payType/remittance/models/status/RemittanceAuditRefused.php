<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/20
 * Time: 上午9:50
 */

namespace app\common\modules\payType\remittance\models\status;

use app\common\models\Process;
use app\common\modules\payType\remittance\models\process\RemittanceAuditProcess;

class RemittanceAuditRefused
{
    /**
     * @param Process $process
     * @throws \Exception
     */
    public function handle(Process $process)
    {
        $process = RemittanceAuditProcess::find($process->id);

        /**
         * @var RemittanceAuditProcess $process
         */
        // 转账流程->下一步
        $process->remittanceRecord->orderPay->currentProcess()->toCloseStatus();

        $process->note = request()->input('note','');
        $process->save();
    }

}