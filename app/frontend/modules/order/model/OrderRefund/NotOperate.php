<?php

/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/3/1
 * Time: 上午11:26
 */

namespace app\frontend\modules\order\model\OrderRefund;

class NotOperate
{
    public function notOperate()
    {
        message('暂不处理', referer());
    }

    public function able($refund_model)
    {
        if ($refund_model['refund_status'] == 0) {
            $this->notOperate();
        }
    }
}