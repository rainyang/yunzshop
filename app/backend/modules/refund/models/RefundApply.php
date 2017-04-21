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


    protected function getTypeInstance()
    {
        switch ($this->refund_type) {
            case self::REFUND_TYPE_MONEY:
                return new RefundMoney();
                break;
            case self::REFUND_TYPE_RETURN:
                return new ReturnGoods();
                break;
            case self::REFUND_TYPE_GOODS:
                return new ReplaceGoods();
                break;
        }
    }
}