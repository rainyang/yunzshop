<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/23
 * Time: 上午11:11
 */

namespace app\frontend\modules\order\controllers;

use app\frontend\modules\goods\service\GoodsService;
use app\frontend\modules\member\service\MemberService;
use app\frontend\modules\order\service\OrderService;

class DisplayController
{
    public function index(){
        $goods_model = GoodsService::getGoodsModel(2);
        var_dump($goods_model->price);exit;
        $order_goods_models = OrderService::getOrderGoodsModel($goods_model);
        var_dump($order_goods_models->price);
        exit;
        exit;
        //$member = Member::getMember();
        $member_model = MemberService::getCurrentMemberModel();

        $goods_models = GoodsService::getGoodsModels([['goods_id'=>1,'total'=>2]]);
        $pre_generated_order_goods_models = OrderService::getPreGeneratedOrderGoodsModels();
        $order_data = OrderService::getPreCreateOrder($goods_group_model,$member_model)->getData();
        ddump($order_data);
    }
}