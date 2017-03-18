<?php

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/14
 * Time: 上午10:40
 */

namespace app\frontend\modules\dispatch\listeners\types;

use app\common\events\dispatch\OnDispatchTypeInfoDisplayEvent;
use app\common\events\order\OrderCreatedEvent;

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
    public function onDisplay(OnDispatchTypeInfoDisplayEvent $event){
        $this->event = $event;
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
        $event->addMap('express',$data);
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
        dd($data);
        //return ;
        OrderAddress::create($data);
    }
    public function subscribe($events)
    {
        $events->listen(
            OnDispatchTypeInfoDisplayEvent::class,
            Express::class.'@onDisplay'
        );
        $events->listen(
            \app\common\events\order\OrderCreatedEvent::class,
            Express::class.'@onSave'
        );
    }

}