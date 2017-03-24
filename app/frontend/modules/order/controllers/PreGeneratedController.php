<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/23
 * Time: 上午11:11
 */

namespace app\frontend\modules\order\controllers;

use app\common\components\BaseController;
use app\common\events\discount\OnDiscountInfoDisplayEvent;
use app\common\events\dispatch\OnDispatchTypeInfoDisplayEvent;
use app\frontend\modules\goods\services\GoodsService;
use app\frontend\modules\member\services\MemberService;
use app\frontend\modules\order\services\OrderService;
use app\frontend\modules\shop\services\ShopService;

class PreGeneratedController extends BaseController
{
    public function index()
    {
        //$param = \YunShop::request();
        $param = [
            [
                'goods_id' => 1,
                'total' => 1
            ]
        ];
        $member_model = MemberService::getCurrentMemberModel();
        $shop_model = ShopService::getCurrentShopModel();


        $order_goods_models = OrderService::getOrderGoodsModels($param);
        list($result, $message) = GoodsService::GoodsListAvailable($order_goods_models);
        if ($result === false) {
            return $this->errorJson($message);
        }
        $order_model = OrderService::getPreGeneratedOrder($order_goods_models, $member_model, $shop_model);

        $order = $order_model->toArray();

        $data = [
            'order' => $order
        ];
        $data = array_merge($data, $this->getDiscountEventData($order_model), $this->getDispatchEventData($order_model));
        dump($data);
        return $this->successJson($data);
    }

    private function getDiscountEventData($order_model)
    {
        $Event = new OnDiscountInfoDisplayEvent($order_model);
        event($Event);
        return $Event->getMap();
    }

    private function getDispatchEventData($order_model)
    {
        $Event = new OnDispatchTypeInfoDisplayEvent($order_model);
        event($Event);
        return ['dispatch' => $Event->getMap()];
    }
}