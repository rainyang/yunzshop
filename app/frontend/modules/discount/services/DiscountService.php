<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/18
 * Time: 下午1:46
 */
namespace app\frontend\modules\discount\services;
use app\common\events\discount\OrderDiscountWasCalculated;
use app\common\events\discount\OrderGoodsDiscountWasCalculated;
use app\common\models\Order;
use app\common\models\OrderGoods;
use app\frontend\modules\discount\services\models\GoodsDiscount;
use app\frontend\modules\discount\services\models\OrderDiscount;
use app\frontend\modules\dispatch\services\models\GoodsDispatch;
use app\frontend\modules\goods\services\models\PreGeneratedOrderGoodsModel;
use app\frontend\modules\order\services\models\PreGeneratedOrderModel;


class DiscountService
{
    public static function getPreOrderDiscountModel(PreGeneratedOrderModel $preGeneratedOrderModel){
        /*//触发事件
        $Event = new OrderDiscountWasCalculated($preGeneratedOrderModel);
        event($Event);
        //获取反馈
        $discount_detail = $Event->getData();*/
        return new OrderDiscount($preGeneratedOrderModel);
    }
    public static function getCreatedOrderDiscountModel(Order $order){
        $order->discount_details;
        return new OrderDiscount($order->discount_details);
    }
    public static function getPreOrderGoodsDiscountModel(PreGeneratedOrderGoodsModel $preGeneratedOrderGoodsModel){
        //触发事件
        $Event = new OrderGoodsDiscountWasCalculated($preGeneratedOrderGoodsModel);
        event($Event);
        //获取反馈
        $discount_detail = $Event->getData();
        $GoodsDiscount = new GoodsDiscount($discount_detail);
        return $GoodsDiscount;
    }
    public static function getCreatedOrderGoodsDiscountModel(OrderGoods $OrderGoods){
        $OrderGoods->discount_details;
        return new GoodsDiscount($OrderGoods->discount_details);
    }

}