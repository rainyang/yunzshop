<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/19
 * Time: 下午5:00
 */

namespace app\common\modules\payType\remittance\models\process;

use app\common\models\Process;
use app\common\models\RemittanceRecord;

/**
 * Class RemittanceProcess
 * @package app\common\modules\payType\remittance\models
 * @property RemittanceRecord $transferRecord
 */
class RemittanceProcess extends Process
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function transferRecord()
    {
        return $this->hasOne(RemittanceRecord::class,'process_id');
    }
}