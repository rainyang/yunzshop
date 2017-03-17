<?php

/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/2/24
 * Time: 下午4:35
 */

namespace app\frontend\modules\order\services;

use app\common\models\Order;
use app\common\models\Member;

use app\frontend\modules\goods\services\models\factory\PreGeneratedOrderGoodsModelFactory;
use app\frontend\modules\goods\services\models\GoodsModel;
use app\frontend\modules\order\services\behavior\OrderCancelPay;
use app\frontend\modules\order\services\behavior\OrderSend;
use app\frontend\modules\goods\services\models\Goods;
use app\frontend\modules\order\services\models\PreGeneratedOrderGoodsModel;
use app\frontend\modules\order\services\models\PreGeneratedOrderModel;
use app\frontend\modules\shop\services\models\ShopModel;

class OrderService
{
    /**
     * 获取预下单对象
     * @param array $order_goods_models
     * @param Member|null $member_model
     * @param ShopModel|null $shop_model
     * @return models\PreGeneratedOrderModel
     */
    public static function getPreCreateOrder(array $order_goods_models,Member $member_model=null,ShopModel $shop_model=null){
        $order_model = new PreGeneratedOrderModel($order_goods_models);
        if(isset($member_model)){
            $order_model->setMemberModel($member_model);
        }
        if(isset($shop_model)){
            $order_model->setShopModel($shop_model);
        }
        return $order_model;
    }

    /**
     * 获取订单商品对象数组
     * @param $param
     * @return array
     */
    public static function getOrderGoodsModels($param){
        return (new PreGeneratedOrderGoodsModelFactory())->createOrderGoodsModels($param);
    }

    /**
     * 获取订单商品对象
     * @param GoodsModel $goods_model
     * @return \app\frontend\modules\goods\services\models\PreGeneratedOrderGoodsModel
     */
    public static function getOrderGoodsModel(GoodsModel $goods_model){
        return (new PreGeneratedOrderGoodsModelFactory())->createOrderGoodsModel($goods_model);

    }
    /**
     * 获取订单号
     * @param GoodsModel $goods_model
     * @return \app\frontend\modules\goods\services\models\PreGeneratedOrderGoodsModel
     */
    public static function createOrderSN(){
        return m('common')->createNO('order', 'ordersn', 'SH');
    }

    /**
     * 取消付款
     * @param Order $order_model
     * @return array
     */
    public static function orderCancelPay(Order $order_model){
        $Cancel_Pay = new OrderCancelPay($order_model);
        if(!$Cancel_Pay->cancelable()){
            return [false,$Cancel_Pay->getMessage()];
        }
        if(!$Cancel_Pay->cancelPay()){
            return [false,$Cancel_Pay->getMessage()];
        }
        return [true,$Cancel_Pay->getMessage()];
    }
    /**
     * 发货
     * @param Order $order_model
     * @return array
     */
    public static function orderSend(Order $order_model){
        $Cancel_Pay = new OrderSend($order_model);
        if(!$Cancel_Pay->cancelable()){
            return [false,$Cancel_Pay->getMessage()];
        }
        if(!$Cancel_Pay->cancelPay()){
            return [false,$Cancel_Pay->getMessage()];
        }
        return [true,$Cancel_Pay->getMessage()];
    }

    public static function orderPay(){

    }
}