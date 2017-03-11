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
use app\frontend\modules\order\services\models\factory\OrderModelFactory;
use app\frontend\modules\order\services\models\factory\PreGeneratedOrderModelFactory;
use app\frontend\modules\goods\services\models\Goods;
use app\frontend\modules\order\services\models\PreGeneratedOrderGoodsModel;
use app\frontend\modules\shop\services\models\ShopModel;

class OrderService
{
    //预下单
    public static function getPreCreateOrder(array $order_goods_models,Member $member_model=null,ShopModel $shop_model=null){
        $order_model = (new PreGeneratedOrderModelFactory())->createOrderModel($order_goods_models);
        if(isset($member_model)){
            $order_model->setMemberModel($member_model);
        }
        if(isset($shop_model)){
            $order_model->setShopModel($shop_model);
        }
        return $order_model;
    }
    public static function getOrderGoodsModels($param){
        return (new PreGeneratedOrderGoodsModelFactory())->createOrderGoodsModels($param);
    }
    public static function getOrderGoodsModel(GoodsModel $goods_model){
        return (new PreGeneratedOrderGoodsModelFactory())->createOrderGoodsModel($goods_model);

    }
    public static function createOrderSN(){
        return m('common')->createNO('order', 'ordersn', 'SH');
    }

}