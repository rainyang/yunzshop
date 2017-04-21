<?php

namespace app\backend\modules\refund\models;

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/21
 * Time: 下午2:24
 */
class RefundApply extends \app\common\models\refund\RefundApply
{
    public function reject($data)
    {
        $this->status = self::REJECT;
        $this->reject_reason = $data['reject_reason'];
        return $this->save();
    }

    public function pass($data)
    {
        $this->status = self::WAIT_REFUND;
        return $this->save();
    }

    public function consensus($data)
    {
        $this->status = self::CONSENSUS;
        return $this->save();
    }
}