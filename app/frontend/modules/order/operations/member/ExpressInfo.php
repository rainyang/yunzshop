<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/8/1
 * Time: 下午6:43
 */
namespace app\frontend\modules\order\operations\member;

use app\frontend\modules\order\operations\OrderOperation;
use app\common\models\DispatchType;

class ExpressInfo extends OrderOperation
{
    public function getApi()
    {
        return 'dispatch.express';
    }
    public function getName()
    {
        return '物流信息';
    }

    public function getValue()
    {
        return static::EXPRESS;
    }
    public function enable()
    {
        // 虚拟
        if ($this->order->isVirtual()) {
            return false;
        }
        // 门店自提、配送站自提、配送站送货
        if (in_array($this->order->dispatch_type_id, [DispatchType::SELF_DELIVERY, DispatchType::DELIVERY_STATION_SELF, DispatchType::DELIVERY_STATION_SEND])) {
            return false;
        }
        return true;
    }
}