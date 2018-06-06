<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/6
 * Time: 下午3:41
 */

namespace app\frontend\models;


use app\common\exceptions\AppException;

class OrderPay extends \app\common\models\OrderPay
{
    public function pay()
    {

        if ($this->status > 0) {
            throw new AppException('(ID' . $this->id . '),此流水号已支付');
        }
        if ($this->orders->isEmpty()) {
            throw new AppException('(ID:' . $this->id . ')未找到对应订单');
        }

    }
}