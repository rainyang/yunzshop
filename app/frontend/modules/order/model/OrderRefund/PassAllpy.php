<?php

/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/3/1
 * Time: 上午11:36
 */
namespace app\frontend\modules\order\model\OrderRefund;
use app\common\servicesModel\OrderRefund;
class PassApply
{
    public function passApply($refund_model)
    {
        OrderRefund::updateOrderRefund($refund_model);
    }

    public function able($refund_model)
    {
        if ($refund_model['refund_status'] == 3) {
            $this->passApply($refund_model);
        }
    }
}