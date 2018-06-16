<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/8
 * Time: 下午3:58
 */

namespace app\frontend\modules\payType;

use app\common\models\Flow;
use app\frontend\modules\payType\remittance\PreTransferRecord;
use app\frontend\modules\payType\remittance\RemittanceFlow;

class Remittance extends BasePayType
{
    public function applyPay()
    {
        $flow = Flow::where('code',RemittanceFlow::class)->first();
        $this->orderPay->flows()->save($flow);


        $a = $this->orderPay->currentProcess();
        dump($a);

//        $transferRecord = new PreTransferRecord();
//        $transferRecord->report_url=$option['report_url'];
//        $transferRecord->setOrderPay($this->orderPay);
//        $transferRecord->save();
    }
}
