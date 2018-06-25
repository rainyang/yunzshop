<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/19
 * Time: 下午5:00
 */

namespace app\common\modules\payType\remittance\models\process;

use app\common\models\RemittanceRecord;
use app\common\modules\audit\process\models\AuditProcess;

/**
 * Class RemittanceProcess
 * @package app\common\modules\payType\remittance\models
 * @property RemittanceRecord $remittanceRecord
 */
class RemittanceAuditProcess extends AuditProcess
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function remittanceRecord()
    {
        return $this->belongsTo(RemittanceRecord::class,'model_id');
    }
}