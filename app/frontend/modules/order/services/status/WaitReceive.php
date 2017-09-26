<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/2
 * Time: 下午4:55
 */

namespace app\frontend\modules\order\services\status;


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

    public function getButton(){
        return [
            'name' => "确认{$this->name}",
            'api' => $this->api,
            'value' => $this->value
        ];
    }
    public function getButtonModels()
    {
        $result[] = $this->getButton();
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