<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/3/1
 * Time: 下午1:50
 */

namespace app\frontend\modules\order\model\OrderRefund;
use app\common\servicesModel\OrderRefund;

class PassRefund
{
    public function passRefund($refund_model)
    {
        OrderRefund::updateOrderRefund($refund_model);
    }

    public function able($refund_model)
    {
        if ($refund_model['refund_status'] == 1) {
            $this->passRefund($refund_model);
        }
    }
}