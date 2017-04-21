<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/21
 * Time: 下午4:54
 */

namespace app\backend\modules\refund\models;


abstract class RefundType
{
    /**
     * @var $refundApply RefundApply
     */
    protected $refundApply;

    public function __construct($refundApply)
    {
        $this->refundApply = $refundApply;
    }

    public function reject($data)
    {
        $this->refundApply->status = RefundApply::REJECT;
        $this->refundApply->reject_reason = $data['reject_reason'];
        return $this->refundApply->save();
    }

    public function consensus($data)
    {
        $this->refundApply->status = RefundApply::CONSENSUS;
        return $this->refundApply->save();
    }

    public function refundMoney()
    {
        $this->refundApply->status = RefundApply::COMPLETE;
        $this->refundApply->price = $this->refundApply->order->price;//todo
        return $this->refundApply->save();
    }
}