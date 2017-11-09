<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/2
 * Time: 下午4:55
 */

namespace app\frontend\modules\order\services\status;


use app\common\models\Order;

class WaitPay extends Status
{
    private $order;
    protected $name = '付款';
    protected $value;
    protected $api = 'order.operation.pay';

    public function __construct(Order $order)
    {
        $this->value = static::PAY;

        $this->order = $order;
    }

    public function getStatusName()
    {
        return '待付款';
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
            $result[] = [
                'name' => '取消订单',
                'api' => 'order.operation.close',
                'value' => static::CANCEL //todo
            ];
        return $result;
    }
    public function getButtonModels()
    {
        $result[] = $this->getNextStatusButton();
        $result = array_merge($result,$this->getOtherButtons());

        return $result;
    }
}