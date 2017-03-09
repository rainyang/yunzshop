<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/23
 * Time: 上午11:11
 */

namespace app\frontend\modules\order\controllers;

use app\common\components\BaseController;
use app\frontend\modules\member\services\MemberService;
use app\frontend\modules\order\services\OrderService;
use app\frontend\modules\shop\services\ShopService;

class PreGeneratedController extends BaseController
{
    public function index(){
        //$param = \YunShop::request();
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
        $order_model = OrderService::getPreCreateOrder($order_goods_models,$member_model,$shop_model);
        $order = $order_model->toArray();
        $data = [
            'order'=>$order
        ];
        return $this->successJson($data);
        dd($order);
    }
}