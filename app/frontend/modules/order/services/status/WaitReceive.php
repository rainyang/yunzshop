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
    private $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
        if ($this->order->dispatch_type == DispatchType::SELF_DELIVERY) {
            // 自提
            $this->name = '使用';
        }
        if ($this->order->dispatch_type == DispatchType::STORE_DELIVERY) {
            // 商家配送
            $this->name = '核销';
        }
    }

    public function getStatusName()
    {
        return "待{$this->name}";
    }

    public function getButtonModels()
    {
        $result[] = [
            'name' => "确认{$this->name}",
            'api' => 'order.operation.receive',
            'value' => static::COMPLETE //todo
        ];
        // 确认核销
        // 确认
        if (!$this->order->isVirtual()) {
            $result[] = [
                'name' => '查看物流', //todo 原来商城的逻辑是, 当有物流单号时, 才显示"查看物流"按钮
                'api' => 'dispatch.express',
                'value' => static::EXPRESS
            ];
        }
        //$result = array_merge($result,self::getRefundButtons($this->order));
        return $result;
    }
}