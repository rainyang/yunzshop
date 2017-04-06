<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/28
 * Time: 上午10:39
 */

namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;
use app\common\components\BaseController;
use app\common\events\cart\GroupingCartEvent;
use app\common\exceptions\AppException;
use app\frontend\modules\goods\services\GoodsService;
use app\frontend\modules\member\models\MemberCart;
use app\frontend\modules\member\services\MemberService;
use app\frontend\modules\order\services\OrderService;
use app\frontend\modules\shop\services\ShopService;

class CreateController extends ApiController
{
    private function getGroupingCart()
    {
        $params = \YunShop::request()->get();
        $params['goods'][] = [
            'goods_id' => 2,
            'total' => 1
        ];

        $event = new GroupingCartEvent();
        event($event);

        $goods_ids = [];
        foreach ($params['goods'] as $goods_params) {
            if (!$event->getMap()['goods_ids']) {
                foreach ($event->getMap()['goods_ids'] as $key => $goods_id) {
                    if ($key == $goods_params['goods_id']) {
                        $goods_ids['plugin'][] = new MemberCart($goods_params);
                    } else {
                        $goods_ids['shop'][] = new MemberCart($goods_params);
                    }
                }
            } else {
                $goods_ids['shop'][] = new MemberCart($goods_params);
            }
        }
        if(!count($goods_ids)){
            throw new AppException('分单失败');
        }
        return $goods_ids;
    }

    private function getMemberCarts(){
        return $this->getGroupingCart();
        $params = \YunShop::request()->get();

        $result = [];
        foreach ($params['goods'] as $goods_params){
            $result[] = new MemberCart($goods_params);
        }
        return $result;
    }
    public function index(){
        //dd(defined('IS_TEST'));exit;
        /*if (!defined('IS_TEST')) {
            return;
        }*/
        $params = \YunShop::request()->get();
        //$this->validator($params['goods']);
        $member_model = MemberService::getCurrentMemberModel();

        $shop_model = ShopService::getCurrentShopModel();
        //todo 根据参数
        foreach ($this->getMemberCarts() as $carts) {
            //echo '<pre>';print_r($carts);exit;
            $order_goods_models = OrderService::getOrderGoodsModels($carts);

            list($result, $message) = GoodsService::GoodsListAvailable($order_goods_models);
            if ($result === false) {
                return $this->errorJson($message);
            }
            $order_model = OrderService::getPreGeneratedOrder($order_goods_models,$member_model,$shop_model);
            $order_model->generate();
        }
        /*$order_goods_models = OrderService::getOrderGoodsModels($this->getMemberCarts());

        list($result, $message) = GoodsService::GoodsListAvailable($order_goods_models);
        if ($result === false) {
            return $this->errorJson($message);
        }
        $order_model = OrderService::getPreGeneratedOrder($order_goods_models,$member_model,$shop_model);
        $order_model->generate();*/
        $this->successJson();
    }
    private function validator($params){
        if(!is_array($params)){
            throw new AppException('请选择下单商品(非数组)');
        }
        if(!count($params)){
            throw new AppException('请选择下单商品(空数组)');
        }
        foreach ($params as $param){
            dd($param);
            exit;
            if(!isset($param['goods_id'])){
                throw new AppException('请选择下单商品(缺少goods_id)');
            }
            if(!isset($param['total'])){
                throw new AppException('请选择下单商品(缺少total)');
            }
        }
    }
}