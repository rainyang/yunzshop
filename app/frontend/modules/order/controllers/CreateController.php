<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/28
 * Time: 上午10:39
 */

namespace app\frontend\modules\order\controllers;

use app\frontend\modules\member\service\MemberService;
use app\frontend\modules\order\service\OrderService;
use app\frontend\modules\shop\service\ShopService;

class CreateController
{
    public function index(){
        $param = [[1,1],[2,1]];
        $member_model = MemberService::getCurrentMemberModel();
        $shop_model = ShopService::getCurrentShopModel();
        //todo 根据参数
        $order_goods_models = OrderService::getOrderGoodsModels($param);
        $order_model = OrderService::getPreCreateOrder($order_goods_models,$member_model,$shop_model);
        var_dump($order_model->price);exit;
        $order_model->generate();
    }

}