<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/20
 * Time: 下午8:14
 */

namespace app\common\models\refund\type;


use app\common\models\refund\RefundApply;

class ReturnGoods extends RefundApply
{
    const WAIT_SEND = '1';//待发货
    const WAIT_RECEIVE = '2';//待收货
    protected $refund_type_name = '退货退款';

    protected function getStatusNameMapping()
    {
        $result = parent::getButtonModelsAttribute();
        $result += [self::WAIT_SEND => '待退货',
            self::WAIT_RECEIVE => '待收货',];
        return $result;
    }

    public function getButtonModelsAttribute()
    {
        $result = parent::getButtonModelsAttribute();
        if ($this->status == self::WAIT_SEND) {
            $result[] = [
                'name' => '填写快递',
                'api' => 'refund.send',
                'value' => 2
            ];
        }
        return $result;
    }

}