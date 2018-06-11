<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/8
 * Time: 下午3:58
 */

namespace app\frontend\modules\orderPay\payType;


class Remittance extends BasePayType
{
    public function applyPay($option)
    {
        $transferRecord = new PreTransferRecord();
        $transferRecord->report_url=$option['report_url'];
        $transferRecord->setOrderPay($this->orderPay);
    }
}
