<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/23
 * Time: 上午11:11
 */

namespace app\frontend\modules\order\controllers;

use app\frontend\modules\member\service\MemberService;
use app\frontend\modules\order\service\OrderService;
use app\frontend\modules\shop\service\ShopService;

class DisplayController
{
    public function index(){

        $param = [
            [
                'goods_id' => 1,
                'total' => 1
            ], [
                'goods_id' => 2,
                'total' => 2
            ]
        ];
        $member_model = MemberService::getCurrentMemberModel();
        $shop_model = ShopService::getCurrentShopModel();
        //todo 根据参数
        $order_goods_models = OrderService::getOrderGoodsModels($param);
        $order_model = OrderService::getPreCreateOrder($order_goods_models,$member_model,$shop_model);
    }
}