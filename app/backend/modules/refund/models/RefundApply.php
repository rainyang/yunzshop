<?php

namespace app\backend\modules\refund\models;
use app\backend\modules\refund\models\type\RefundMoney;
use app\backend\modules\refund\models\type\ReplaceGoods;
use app\backend\modules\refund\models\type\ReturnGoods;
use app\common\exceptions\AdminException;

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/21
 * Time: 下午2:24
 */
class RefundApply extends \app\common\models\refund\RefundApply
{
    protected $typeInstance;

    public function refundMoney()
    {
        return $this->getTypeInstance()->refundMoney();
    }
    public function reject($data)
    {
        return $this->getTypeInstance()->reject($data);
    }
    public function pass($data)
    {
        return $this->getTypeInstance()->pass($data);
    }
    public function consensus($data)
    {
        return $this->getTypeInstance()->consensus($data);
    }
    protected function getTypeInstance()
    {
        if (!isset($this->typeInstance)) {
            switch ($this->refund_type) {
                case self::REFUND_TYPE_MONEY:
                    $this->typeInstance = new RefundMoney($this);
                    break;
                case self::REFUND_TYPE_RETURN:
                    $this->typeInstance = new ReturnGoods($this);
                    break;
                case self::REFUND_TYPE_GOODS:
                    $this->typeInstance = new ReplaceGoods($this);
                    break;
                default:
                    throw new AdminException('退款类型不存在');
                    break;
            }
        }
        return $this->typeInstance;

    }
}