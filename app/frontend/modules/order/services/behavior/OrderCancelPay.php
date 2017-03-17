<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/2/28
 * Time: 上午11:19
 * comment: 取消支付
 */

namespace app\frontend\modules\order\services\behavior;

use app\common\events\order\AfterOrderCanceledEvent;
use app\common\events\order\BeforeOrderCancelPayEvent;
use app\frontend\modules\order\services\models\OperationValidator;

class OrderCancelPay extends OrderOperation
{
    public function cancelPay()
    {
        $this->order_model->status = 0;
        $result = $this->order_model->save();
        event(new AfterOrderCanceledEvent($this->order_model));
        return $result;
    }

    public function cancelable()
    {
        $Event = new BeforeOrderCancelPayEvent($this->order_model);
        event($Event);
        if ($Event->hasOpinion()) {
            $this->message = $Event->getOpinion()->message;
            return $Event->getOpinion()->result;
        }
        if ($this->order_model['status'] != 1) {
            $this->message = '订单状态不满足取消付款操作';
            return false;
        }
        return true;
    }
}