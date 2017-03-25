<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/28
 * Time: 上午10:39
 */

namespace app\frontend\modules\order\controllers;

use app\common\components\BaseController;
use app\frontend\modules\member\services\MemberService;
use app\frontend\modules\order\services\OrderService;
use app\frontend\modules\shop\services\ShopService;

class CreateController extends BaseController
{
    public function index(){
        $param = [
            [
                'goods_id' => 1,
                'total' => 1
            ]
        ];
        $member_model = MemberService::getCurrentMemberModel();

        $shop_model = ShopService::getCurrentShopModel();
        //todo 根据参数
        $order_goods_models = OrderService::getOrderGoodsModels($param);
        $order_model = OrderService::getPreGeneratedOrder($order_goods_models,$member_model,$shop_model);
        $order_model->generate();
        exit;
    }

}