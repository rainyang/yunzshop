<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/21
 * Time: 下午4:54
 */

namespace app\backend\modules\refund\models\type;


use app\backend\modules\refund\models\RefundApply;
use app\common\exceptions\AdminException;

abstract class RefundType
{
    /**
     * @var $refundApply RefundApply
     */
    protected $refundApply;

    protected function validate(array $allowBeforeStatus, $operationName)
    {
        if (!in_array($this->refundApply->status, $allowBeforeStatus)) {
            throw new AdminException($this->refundApply->status_name . '的退款申请,无法执行' . $operationName . '操作');
        }
    }

    public function __construct($refundApply)
    {
        $this->refundApply = $refundApply;
    }

    /**
     * 驳回
     * @param $data
     * @return bool
     */
    public function reject($data)
    {
        $this->validate([RefundApply::WAIT_CHECK], '驳回');
        $this->refundApply->status = RefundApply::REJECT;
        $this->refundApply->reject_reason = $data['reject_reason'];
        return $this->refundApply->save();
    }

    /**
     * 手动退款
     * @return bool
     */
    public function consensus()
    {
        $this->validate([RefundApply::WAIT_CHECK], '手动退款');

        $this->refundApply->status = RefundApply::CONSENSUS;
        return $this->refundApply->save();
    }

    /**
     * todo ????
     * @return bool
     */
    public function refundMoney()
    {
        //$this->validate([RefundApply::WAIT_CHECK],'手动退款');

        $this->refundApply->status = RefundApply::COMPLETE;
        $this->refundApply->price = $this->refundApply->order->price;//todo
        return $this->refundApply->save();
    }

    //abstract public function pass();

}