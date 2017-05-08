<?php

namespace app\backend\modules\refund\models;

use app\backend\modules\order\models\Order;
use app\backend\modules\refund\models\type\RefundMoney;
use app\backend\modules\refund\models\type\ExchangeGoods;
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

    public function pass()
    {
        return $this->getTypeInstance()->pass();
    }

    public function consensus()
    {
        return $this->getTypeInstance()->consensus();
    }

    public function receiveReturnGoods()
    {
        //todo 补充当退款类型实例请求 收货请求时的提示
        return $this->getTypeInstance()->receiveReturnGoods();
    }

    protected function getTypeInstance()
    {
        if (!isset($this->typeInstance)) {
            switch ($this->refund_type) {
                case self::REFUND_TYPE_REFUND_MONEY:
                    $this->typeInstance = new RefundMoney($this);
                    break;
                case self::REFUND_TYPE_RETURN_GOODS:
                    $this->typeInstance = new ReturnGoods($this);
                    break;
                case self::REFUND_TYPE_EXCHANGE_GOODS:
                    $this->typeInstance = new ExchangeGoods($this);
                    break;
                default:
                    throw new AdminException('退款类型不存在');
                    break;
            }
        }

        return $this->typeInstance;

    }



    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}