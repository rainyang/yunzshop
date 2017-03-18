<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/18
 * Time: 下午1:46
 */
namespace app\frontend\modules\dispatch\services;
use app\common\events\dispatch\OrderDispatchWasCalculated;
use app\common\models\Order;
use app\frontend\modules\dispatch\services\models\OrderDispatch;
use app\frontend\modules\order\services\models\PreGeneratedOrderModel;


class DispatchService
{
    public static function getPreOrderDispatchModel(PreGeneratedOrderModel $preGeneratedOrderModel){
        //触发事件
        $Event = new OrderDispatchWasCalculated($preGeneratedOrderModel);
        event($Event);
        //获取反馈
        $dispatch_detail = $Event->getData();
        return new OrderDispatch($dispatch_detail);
    }
    public static function getCreatedOrderDispatchModel(Order $order){
        $order->dispatch_details;
        return new OrderDispatch($order->dispatch_details);
    }
}