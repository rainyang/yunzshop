<?php
namespace app\frontend\modules\order\service;

//use app\frontend\modules\goods\model\factory\GoodsModelFactory;
use app\frontend\modules\goods\model\GoodsGroupModel;
use app\frontend\modules\goods\model\MemberModel;
use app\frontend\modules\order\model\factory\PreCreateOrderModelFactory;
use app\modules\goods\model\frontend\Goods;

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/21
 * Time: 下午4:01
 */
class OrderService
{
    //预下单
    public static function getPreCreateOrder(GoodsGroupModel $goods_group_model,MemberModel $member_model){
        $order_model = (new PreCreateOrderModelFactory)->getOrderModel();
        //$goods_models = (new GoodsModelFactory)->getGoodsModel();
        $order_model->addGoods($goods_group_model);
        $order_model->addMember($member_model);
        return $order_model->getData();
    }
    function pay(){

    }
}