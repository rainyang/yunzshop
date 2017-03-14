<?php

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/14
 * Time: 上午10:40
 */

namespace app\frontend\modules\order\listeners\dispatch\types;

use app\common\events\OrderCreatedEvent;
use app\common\events\PreGeneratedOrderDisplayEvent;

use app\common\models\OrderAddress;

class Express
{
    private $event;
    public function onSave(OrderCreatedEvent $even)
    {
        $this->event = $even;
        if (!$this->needDispatch()) {
            return;
        }
        //保存信息
        $this->saveExpressInfo();

        return;
    }
    public function onDisplay(PreGeneratedOrderDisplayEvent $even){
        $this->event = $even;
        if (!$this->needDispatch()) {
            return;
        }
        //返回信息 todo 需要获取用户当前默认地址

        $data = [
            'dispatch_type_id'=>1,
            'address'=>'默认地址(死值)',
            'mobile'=>'18545571024',
            'realname'=>'默认姓名(死值)',
        ];
        $even->addDispatchInfo($data);
        return ;
    }
    private function needDispatch(){
        return true;
    }
    private function saveExpressInfo(){
        $params = \YunShop::request();
        //dd($this->event->getOrderModel());exit;
        echo '收货地址插入数据为';
        $data = [
            'order_id'=>$this->event->getOrderModel()->id,
            'address'=>$params['address']['address'],
            'mobile'=>$params['address']['mobile'],
            'realname'=>$params['address']['realname'],
        ];
        var_dump($data);
        return ;
        OrderAddress::save($data);
    }
    public function subscribe($events)
    {
        $events->listen(
            \app\common\events\PreGeneratedOrderDisplayEvent::class,
            \app\frontend\modules\order\listeners\dispatch\types\Express::class.'@onDisplay'
        );
        $events->listen(
            \app\common\events\OrderCreatedEvent::class,
            \app\frontend\modules\order\listeners\dispatch\types\Express::class.'@onSave'
        );
    }

}