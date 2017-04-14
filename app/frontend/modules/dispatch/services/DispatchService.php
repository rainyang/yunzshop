<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/18
 * Time: 下午1:46
 */
namespace app\frontend\modules\dispatch\services;
use app\common\events\dispatch\OrderDispatchWasCalculated;
use app\common\events\dispatch\OrderGoodsDispatchWasCalculated;
use app\common\models\Order;
use app\common\models\OrderGoods;
use app\frontend\modules\dispatch\services\models\GoodsDispatch;
use app\frontend\modules\dispatch\services\models\OrderDispatch;
use app\frontend\modules\goods\services\models\PreGeneratedOrderGoodsModel;
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
    public static function getPreOrderGoodsDispatchModel(PreGeneratedOrderGoodsModel $preGeneratedOrderGoodsModel){
        //触发事件
        $Event = new OrderGoodsDispatchWasCalculated($preGeneratedOrderGoodsModel);
        event($Event);
        //获取反馈
        $dispatch_detail = $Event->getData();
        return new GoodsDispatch($dispatch_detail);
    }
    public static function getCreatedOrderGoodsDispatchModel(OrderGoods $OrderGoods){
        $OrderGoods->dispatch_details;
        return new GoodsDispatch($OrderGoods->dispatch_details);
    }
}