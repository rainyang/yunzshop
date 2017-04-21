<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/21
 * Time: 下午4:34
 */

namespace app\backend\modules\refund\models;


class RefundMoney extends RefundType
{

    public function pass($data)
    {
        $this->refundApply->status = RefundApply::WAIT_SEND;
        return $this->refundApply->save();
    }

}