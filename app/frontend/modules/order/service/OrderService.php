<?php
namespace app\frontend\modules\order\service;

use app\frontend\modules\goods\model\factory\PreGeneratedOrderGoodsModelFactory;
use app\frontend\modules\goods\model\GoodsModel;
use app\frontend\modules\member\model\MemberModel;
use app\frontend\modules\order\model\factory\OrderModelFactory;
use app\frontend\modules\order\model\factory\PreGeneratedOrderModelFactory;
use app\frontend\modules\goods\model\Goods;
use app\frontend\modules\order\model\PreGeneratedOrderGoodsModel;
use app\frontend\modules\shop\model\ShopModel;

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/21
 * Time: 下午4:01
 */
class OrderService
{
    //预下单
    public static function getPreCreateOrder(array $order_goods_models,MemberModel $member_model=null,ShopModel $shop_model=null){
        $order_model = (new PreGeneratedOrderModelFactory())->createOrderModel($order_goods_models);
        if(isset($member_model)){
            $order_model->setMemberModel($member_model);
        }
        if(isset($shop_model)){
            $order_model->setShopModel($shop_model);
        }
        return $order_model;
    }
    //订单详情
    public static function getOrderModel($order_status,$order_id){
        $order_model = OrderModelFactory::createOrderModel($order_status,$order_id);
        return $order_model;
    }
    //订单列表
    public static function getOrderModels($db_order_models){
        $order_list_models = OrderModelFactory::createOrderModels($db_order_models);
        return $order_list_models;
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
    public static function getOrderStatusName($status){
        $status_name_mapping = [
            '-1' => "已关闭",
            "0" => "待付款",
            "1" => "待发货",
            "2" => "待收货",
            "3" => "已完成",
        ];
        if(isset($status_name_mapping[$status])){
            return $status_name_mapping[$status];
        }
        return '';
    }

    public static function getButtonModels($status){

    }


}