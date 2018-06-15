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
ddd
exit;
        $flow = Flow::where('code',RemittanceFlow::class)->first();
        dd($this->orderPay->flows());
        exit;

        $this->orderPay->flows()->save($flow);
        $a = $this->orderPay->flow();
        dd($a);
        exit;

        exit;
//        $transferRecord = new PreTransferRecord();
//        $transferRecord->report_url=$option['report_url'];
//        $transferRecord->setOrderPay($this->orderPay);
//        $transferRecord->save();
    }
}
