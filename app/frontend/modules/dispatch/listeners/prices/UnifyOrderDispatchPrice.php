<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/11
 * Time: 上午10:00
 */

namespace app\frontend\modules\dispatch\listeners\prices;

use app\common\events\dispatch\OrderDispatchWasCalculated;

class UnifyOrderDispatchPrice
{
    private $event;
    public function handle(OrderDispatchWasCalculated $even)
    {
        $this->event = $even;
        if (!$this->needDispatch()) {
            return;
        }
        //返回给事件
        $even->addData($this->getDispatchDetails());

        return;
    }
    //订单满足本插件 todo 需要重写
    public function needDispatch(){
        return false;
    }
    private function getDispatchDetails(){
        $details = [
            'name'=>'统一运费',
            'id'=>'1',
            'value'=>'9',
            'price'=>$this->getDispatchPrice(),
            'plugin'=>'0',
        ];

        return $details;
    }
    //订单统一运费算法
    private function getDispatchPrice(){
        //取商品数组 统一运费的最大值
        $result = 0;
        //dd($this->even->getOrderModel()->getOrderGoodsModels());
        foreach ($this->event->getOrderModel()->getOrderGoodsModels() as $order_goods){

            foreach ($order_goods->getDispatchDetails() as $dispatch_detail){

                //将订单统一运费类型的价格  取最大值
                if($dispatch_detail['id'] == 1){

                    $result = max($result,$dispatch_detail['price']);

                }
            }
        }
        return $result;
    }
}