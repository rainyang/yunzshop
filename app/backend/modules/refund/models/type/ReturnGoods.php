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
    public function pass($data)
    {
        $this->validate([RefundApply::WAIT_CHECK],'通过');

        $this->refundApply->status = RefundApply::WAIT_REFUND;
        return $this->refundApply->save();
    }

    //public function
}