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
use app\common\events\order\AfterOrderCreatedEvent;
use app\common\exceptions\AppException;
use app\frontend\modules\goods\services\GoodsService;
use app\frontend\modules\member\models\MemberCart;
use app\frontend\modules\member\services\MemberCartService;
use app\frontend\modules\member\services\MemberService;
use app\frontend\modules\order\services\OrderService;
use app\frontend\modules\shop\services\ShopService;

class CreateController extends ApiController
{
    private function getGroupingCart()
    {
        $params = \YunShop::request()->get();
        $this->validator($params['goods']);

        $event = new GroupingCartEvent();
        event($event);

        $goods_ids = [];
        foreach ($params['goods'] as $goods_params) {
            if ($event->getMap()['goods_ids']) {
                foreach ($event->getMap()['goods_ids'] as $key => $goods_id) {
                    if ($key == $goods_params['goods_id']) {
                        $goods_ids['plugin'][] = MemberCartService::newMemberCart($goods_params);
                    } else {
                        $goods_ids['shop'][] = MemberCartService::newMemberCart($goods_params);
                    }
                }
            } else {
                $goods_ids['shop'][] = MemberCartService::newMemberCart($goods_params);
            }
        }
        if(!count($goods_ids)){
            throw new AppException('分单失败');
        }
        return $goods_ids;
    }

    private function getMemberCarts(){
        return $this->getGroupingCart();
    }
    public function index(){

        $member_model = MemberService::getCurrentMemberModel();

        $shop_model = ShopService::getCurrentShopModel();
        //todo 根据参数
        foreach ($this->getMemberCarts() as $carts) {
            $order_goods_models = OrderService::getOrderGoodsModels($carts);

            $order_model = OrderService::getPreGeneratedOrder($order_goods_models,$member_model,$shop_model);
            $result = $order_model->generate();
            if(!$result){
                throw new AppException('订单生成失败');
            }
            event(new AfterOrderCreatedEvent($order_model));

        }

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

            if(!isset($param['goods_id'])){
                throw new AppException('请选择下单商品(缺少goods_id)');
            }
            if(!isset($param['total'])){
                throw new AppException('请选择下单商品(缺少total)');
            }
        }
    }
}