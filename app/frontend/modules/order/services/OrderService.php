<?php

/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/2/24
 * Time: 下午4:35
 */

namespace app\frontend\modules\order\services;

use app\common\events\discount\OnDiscountInfoDisplayEvent;
use app\common\events\dispatch\OnDispatchTypeInfoDisplayEvent;
use app\common\exceptions\AppException;
use app\common\models\Order;

use app\frontend\modules\goods\services\models\GoodsModel;
use app\frontend\modules\member\models\MemberCart;
use app\frontend\modules\member\services\MemberService;
use app\frontend\modules\order\services\behavior\OrderCancelPay;
use app\frontend\modules\order\services\behavior\OrderCancelSend;
use app\frontend\modules\order\services\behavior\OrderChangePrice;
use app\frontend\modules\order\services\behavior\OrderClose;
use app\frontend\modules\order\services\behavior\OrderDelete;
use app\frontend\modules\order\services\behavior\OrderOperation;
use app\frontend\modules\order\services\behavior\OrderPay;
use app\frontend\modules\order\services\behavior\OrderReceive;
use app\frontend\modules\order\services\behavior\OrderSend;
use app\frontend\modules\goods\services\models\Goods;
use app\frontend\modules\goods\services\models\PreGeneratedOrderGoodsModel;
use app\frontend\modules\order\services\models\PreGeneratedOrderModel;
use app\frontend\modules\shop\services\ShopService;
use Illuminate\Support\Collection;

class OrderService
{
    public static function getOrderData(Order $order)
    {
        $data = [
            'order' => $order->toArray(),
        ];
        $data += self::getDiscountEventData($order);
        $data += self::getDispatchEventData($order);
        return $data;
    }
    private static function getDiscountEventData($order_model)
    {
        $Event = new OnDiscountInfoDisplayEvent($order_model);
        event($Event);
        return $Event->getMap();
    }

    public static function getDispatchEventData($order_model)
    {
        $Event = new OnDispatchTypeInfoDisplayEvent($order_model);
        event($Event);
        return ['dispatch' => $Event->getMap()];
    }

    /**
     * @param $callback
     * @return Collection
     * @throws AppException
     */
    public static function getMemberCarts($callback)
    {
        $cartIds = [];
        if (!is_array($_GET['cart_ids'])) {
            $cartIds = explode(',', $_GET['cart_ids']);
        }

        if (!count($cartIds)) {
            throw new AppException('参数格式有误');
        }

        $memberCarts = MemberCart::getCartsByIds($cartIds);
        if (!count($memberCarts)) {
            throw new AppException('未找到购物车信息');
        }

        $result = $memberCarts->filter($callback);

        if (!count($result)) {
            throw new AppException('请选择下单商品');
        }
        return $result;
    }

    /**
     * 获取订单商品对象数组
     * @param Collection $memberCarts
     * @return Collection
     * @throws \Exception
     */
    public static function getOrderGoodsModels(Collection $memberCarts)
    {
        $result = new Collection();
        foreach ($memberCarts as $memberCart) {
            if (!($memberCart instanceof MemberCart)) {
                throw new \Exception("请传入" . MemberCart::class . "的实例");
            }
            /**
             * @var $memberCart MemberCart
             */
            $orderGoodsModel = new PreGeneratedOrderGoodsModel($memberCart->toArray());
            $result->push($orderGoodsModel);
        }
        return $result;
    }

    public static function createOrderByMemberCarts(Collection $memberCarts)
    {
        $member = MemberService::getCurrentMemberModel();
        if (!isset($member)) {
            throw new AppException('用户登录状态过期');
        }
        $shop = ShopService::getCurrentShopModel();

        $orderGoodsArr = OrderService::getOrderGoodsModels($memberCarts);
        $order = new PreGeneratedOrderModel(['uid' => $member->uid, 'uniacid' => $shop->uniacid]);
        $order->setOrderGoodsModels($orderGoodsArr);
        return $order;
    }

    /**
     * 获取订单号
     * @return string
     */
    public static function createOrderSN()
    {
        return 'sn' . time();//m('common')->createNO('order', 'ordersn', 'SH');
    }

    private static function OrderOperate(OrderOperation $OrderOperate)
    {
        if (!$OrderOperate->enable()) {
            return [false, $OrderOperate->getMessage()];
        }
        if (!$OrderOperate->execute()) {
            return [false, $OrderOperate->getMessage()];
        }
        return [true, $OrderOperate->getMessage()];
    }

    /**
     * 取消付款
     * @param $param
     * @return array
     */
    public static function orderCancelPay($param)
    {
        $order_model = Order::find($param['order_id']);

        $OrderOperation = new OrderCancelPay($order_model);
        return self::OrderOperate($OrderOperation);
    }

    /**
     * 取消发货
     * @param $param
     * @return array
     */
    public static function orderCancelSend($param)
    {
        $order_model = Order::find($param['order_id']);

        $OrderOperation = new OrderCancelSend($order_model);
        return self::OrderOperate($OrderOperation);
    }

    /**
     * 关闭订单
     * @param $param
     * @return array
     */
    public static function orderClose($param)
    {
        $order_model = Order::find($param['order_id']);

        $OrderOperation = new OrderClose($order_model);
        return self::OrderOperate($OrderOperation);
    }

    /**
     * 用户删除(隐藏)订单
     * @param $param
     * @return array
     */
    public static function orderDelete($param)
    {
        $order_model = Order::find($param['order_id']);

        $OrderOperation = new OrderDelete($order_model);
        return self::OrderOperate($OrderOperation);
    }

    /**
     * 支付订单
     * @param array $param
     * @return array
     */

    public static function orderPay(array $param)
    {
        $order_model = Order::find($param['order_id']);
        $OrderOperation = new OrderPay($order_model);
        return self::OrderOperate($OrderOperation);
    }

    /**
     * 收货
     * @param $param
     * @return array
     */
    public static function orderReceive($param)
    {
        $order_model = Order::find($param['order_id']);

        $OrderOperation = new OrderReceive($order_model);
        return self::OrderOperate($OrderOperation);
    }

    /**
     * 发货
     * @param $param
     * @return array
     */
    public static function orderSend($param)
    {
        $order_model = Order::find($param['order_id']);

        $OrderOperation = new OrderSend($order_model);
        return self::OrderOperate($OrderOperation);
    }

    /**
     * 改变订单价格
     * @param $param
     * @return array
     */
    public static function changeOrderPrice($param)
    {
        $order_model = Order::find($param['order_id']);

        $OrderOperation = new OrderChangePrice($order_model);
        return self::OrderOperate($OrderOperation);
    }
}