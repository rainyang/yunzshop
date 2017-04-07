<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/23
 * Time: 上午11:11
 */

namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;
use app\common\events\cart\GroupingCartIdEvent;
use app\common\events\discount\OnDiscountInfoDisplayEvent;
use app\common\events\dispatch\OnDispatchTypeInfoDisplayEvent;
use app\common\exceptions\AppException;
use app\common\models\Order;
use app\frontend\modules\goods\services\GoodsService;
use app\frontend\modules\member\models\MemberCart;
use app\frontend\modules\member\services\MemberService;
use app\frontend\modules\order\services\models\PreGeneratedOrderModel;
use app\frontend\modules\order\services\OrderService;
use app\frontend\modules\shop\services\ShopService;

class PreGeneratedController extends ApiController
{
    private $param;
    private $memberCarts;

    public function index()
    {

        $this->param['goods'] = [
            'goods_id' => \YunShop::request()->get('goods_id'),
            'total' => \YunShop::request()->get('total'),
            'option_id' => \YunShop::request()->get('option_id'),
        ];
        $this->memberCarts[] = (new MemberCart($this->param['goods']));

        $this->run();
    }

    public function cart()
    {
        if (!isset($_GET['cart_ids'])) {
            return $this->errorJson('请选择要结算的商品');
        }
        if (!is_array($_GET['cart_ids'])) {
            $cartIds = explode(',', $_GET['cart_ids']);
        }
        if (!count($cartIds)) {
            return $this->errorJson('参数格式有误');
        }

        $event = new GroupingCartIdEvent($cartIds);
        event($event);

        $goods_ids = [];
        if ($event->getMap()) {
            $goods_ids[] = $event->getMap();
            $goods_ids[] = array_diff($cartIds, $event->getMap());
        } else {
            $goods_ids[] = $cartIds;
        }

        foreach ($goods_ids as $goods_id) {
            $memberCart = MemberCart::getCartsByIds($goods_id);
            if (!count($memberCart)) {
                throw new AppException('未找到购物车信息');
            }
            $this->memberCarts[] = $memberCart;
        }

        $this->run();
    }

    private function run()
    {
        $member = MemberService::getCurrentMemberModel();
        if (!isset($member)) {
            throw new AppException('用户登录状态过期');
        }
        $shop = ShopService::getCurrentShopModel();

        $orderGoodsModels = [];

        foreach ($this->memberCarts as $memberCart) {
            $orderGoods = OrderService::getOrderGoodsModels($memberCart);
            $orderGoodsModels[] = $orderGoods;
            if (!count($orderGoods)) {

                throw new AppException('未找到商品(ID:)' . $memberCart->goods_id);
            }
        }
        if (!count($orderGoodsModels)) {
            throw new AppException('未找到商品');
        }


        $order_models = [];
        foreach ($orderGoodsModels as $orderGoodsModel) {

            $order = new PreGeneratedOrderModel(['uid'=>$member->uid,'uniacid'=>$shop->uniacid]);
            $order->setOrderGoodsModels($orderGoodsModel);

            $order_models[] = $order;
        }

        $order_data = [];
        $total_price = 0;
        $total_goods_price = 0;
        $total_dispatch_price = 0;
        foreach ($order_models as $order_model) {
            $order = $order_model->toArray();
            $data = [
                'order' => $order
            ];
            $total_price += $order['price'];
            $total_goods_price += $order['goods_price'];
            $total_dispatch_price += $order['dispatch_price'];
            $order_data[] = array_merge($data, $this->getDiscountEventData($order_model));
        }
        $data = compact('total_price', 'total_dispatch_price', 'order_data','total_goods_price');
        $data += $this->getDispatchEventData($order_model);
        return $this->successJson('成功', $data);


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