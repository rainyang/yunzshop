<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/2
 * Time: 下午4:55
 */

namespace app\frontend\modules\order\services\status;


use app\common\models\DispatchType;
use app\common\models\Order;

class WaitReceive extends Status
{
    protected $name = '收货';
    protected $api = 'order.operation.receive';
    protected $value;
    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->value = static::COMPLETE;
    }

    public function getStatusName()
    {
        return "待{$this->name}";
    }

    protected function getNextStatusButton(){
        return [
            'name' => "确认{$this->name}",
            'api' => $this->api,
            'value' => $this->value
        ];
    }
    protected function getOtherButtons(){
        $result = [];
        if (!$this->order->isVirtual()  && !in_array($this->order->dispatch_type_id, [DispatchType::STORE_DELIVERY, DispatchType::SELF_DELIVERY])) {
            $result[] = [
                'name' => '查看物流', //todo 原来商城的逻辑是, 当有物流单号时, 才显示"查看物流"按钮
                'api' => 'dispatch.express',
                'value' => static::EXPRESS
            ];
        }
        return $result;
    }
    public function getButtonModels()
    {
        $result[] = $this->getNextStatusButton();
        $result = array_merge($result,$this->getOtherButtons());

        return $result;
    }
}