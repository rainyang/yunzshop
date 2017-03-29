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
use app\frontend\modules\member\models\MemberCart;
use app\frontend\modules\member\services\MemberService;
use app\frontend\modules\order\services\OrderService;
use app\frontend\modules\shop\services\ShopService;

class PreGeneratedController extends BaseController
{
    private $_param;


    public function index()
    {
        $this->_param['goods'][] = [
            'goods_id'=>\YunShop::request()->get('goods_id'),
            'total'=>\YunShop::request()->get('total'),
            'option_id'=>\YunShop::request()->get('option_id'),
        ];
        $this->run();
    }

    public function cart()
    {
        if(!isset($_GET['cart_ids'])){
            return $this->errorJson('请选择要结算的商品');
        }
        if(!is_array($_GET['cart_ids'])){
            $cart_ids = explode(',',$_GET['cart_ids']);
        }
        if(!count($cart_ids)){
            return $this->errorJson('参数格式有误');
        }
        $cart = MemberCart::getMemberCartByIds($cart_ids);
        //dd($cart);exit;
        $this->_param['goods'] = $cart;
        $this->run();
    }

    private function run()
    {

        $member_model = MemberService::getCurrentMemberModel();
        //dd($member_model);exit;
        if(!isset($member_model)){
            return $this->errorJson('用户登录状态过期');
        }
        $shop_model = ShopService::getCurrentShopModel();

        $order_goods_models = OrderService::getOrderGoodsModels($this->_param['goods']);
        if(!count($order_goods_models)){
            return $this->errorJson('未找到商品');
        }
        //dd($order_goods_models);exit;
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
        //var_dump($data);
        return $this->successJson('成功',$data);
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