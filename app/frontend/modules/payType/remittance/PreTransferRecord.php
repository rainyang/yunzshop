<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/8
 * Time: 下午4:31
 */

namespace app\frontend\modules\payType\remittance;

use app\common\models\OrderPay;
use app\frontend\models\TransferRecord;

class PreTransferRecord extends TransferRecord
{

    public function setOrderPay(OrderPay $orderPay)
    {
        $this->order_pay_id = $orderPay->id;
    }
}