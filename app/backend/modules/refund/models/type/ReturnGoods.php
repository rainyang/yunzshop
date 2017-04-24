<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/21
 * Time: 下午4:34
 */

namespace app\backend\modules\refund\models\type;

use app\backend\modules\refund\models\RefundApply;

class ReturnGoods extends RefundType
{
    public function pass()
    {
        $this->validate([RefundApply::WAIT_CHECK],'通过');

        $this->refundApply->status = RefundApply::WAIT_RETURN_GOODS;
        return $this->refundApply->save();
    }

    public function receiveReturnGoods()
    {
        $this->validate([RefundApply::WAIT_RECEIVE_RETURN_GOODS],'收货');

        $this->refundApply->status = RefundApply::WAIT_REFUND;
        return $this->refundApply->save();
    }
    //public function
}