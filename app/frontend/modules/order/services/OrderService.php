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
use app\common\models\finance\BalanceRecharge;
use app\common\models\Order;

use app\common\models\order\OrderChangeLog;
use app\common\models\order\OrderGoodsChangePriceLog;
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
use app\frontend\modules\order\services\behavior\Send;
use app\frontend\modules\goods\services\models\Goods;
use app\frontend\modules\goods\services\models\PreGeneratedOrderGoodsModel;
use app\frontend\modules\order\services\models\PreGeneratedOrderModel;
use app\frontend\modules\shop\services\ShopService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * 获取订单信息组
     * @param Order $order
     * @return Collection
     */
    public static function getOrderData(Order $order)
    {
        $result = collect();
        $result->put('order', $order->toArray());
        $result->put('discount', self::getDiscountEventData($order));
        $result->put('dispatch', self::getDispatchEventData($order));

        return $result;
    }

    /**
     * 获取优惠信息
     * @param $order_model
     * @return array
     */
    private static function getDiscountEventData($order_model)
    {
        $Event = new OnDiscountInfoDisplayEvent($order_model);
        event($Event);
        return $Event->getMap();
    }

    /**
     * 获取配送信息
     * @param $order_model
     * @return array
     */
    public static function getDispatchEventData($order_model)
    {
        $Event = new OnDispatchTypeInfoDisplayEvent($order_model);
        event($Event);
        return $Event->getMap();
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
        if ($memberCarts->isEmpty()) {
            throw new AppException("(".$memberCarts->goods_id.")未找到订单商品");
        }
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

    /**
     * 根据购物车记录 获取订单信息
     * @param Collection $memberCarts
     * @return PreGeneratedOrderModel
     * @throws AppException
     */
    public static function createOrderByMemberCarts(Collection $memberCarts)
    {
        $member = MemberService::getCurrentMemberModel();
        if (!isset($member)) {
            throw new AppException('用户登录状态过期');
        }

        if ($memberCarts->isEmpty()) {
            return false;
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
        $orderSN = createNo('SN', true);
        while (1) {
            if (!Order::where('order_sn', $orderSN)->first()) {
                break;
            }
            $orderSN = createNo('SN', true);
        }
        return $orderSN;
    }

    private static function OrderOperate(OrderOperation $orderOperation)
    {

        if (!$orderOperation->enable()) {
            return [false, $orderOperation->getMessage()];
        }
        DB::transaction(function () use ($orderOperation) {
            if (!$orderOperation->execute()) {
                return [false, $orderOperation->getMessage()];
            }
        });
        return [true, $orderOperation->getMessage()];
    }

    /**
     * 取消付款
     * @param $param
     * @return array
     */
    public static function orderCancelPay($param)
    {
        $orderOperation = OrderCancelPay::find($param['order_id']);

        return self::OrderOperate($orderOperation);
    }

    /**
     * 取消发货
     * @param $param
     * @return array
     */
    public static function orderCancelSend($param)
    {
        $orderOperation = OrderCancelSend::find($param['order_id']);

        return self::OrderOperate($orderOperation);
    }

    /**
     * 关闭订单
     * @param $param
     * @return array
     */
    public static function orderClose($param)
    {
        $orderOperation = OrderClose::find($param['order_id']);

        return self::OrderOperate($orderOperation);
    }

    /**
     * 用户删除(隐藏)订单
     * @param $param
     * @return array
     */
    public static function orderDelete($param)
    {
        $orderOperation = OrderDelete::find($param['order_id']);

        return self::OrderOperate($orderOperation);
    }

    /**
     * 支付订单
     * @param array $param
     * @return array
     */

    public static function orderPay(array $param)
    {
        $orderOperation = OrderPay::find($param['order_id']);
        return self::OrderOperate($orderOperation);
    }

    /**
     * 收货
     * @param $param
     * @return array
     */
    public static function orderReceive($param)
    {
        $orderOperation = OrderReceive::find($param['order_id']);

        return self::OrderOperate($orderOperation);
    }

    /**
     * 发货
     * @param $param
     * @return array
     */
    public static function orderSend($param)
    {
        $orderOperation = OrderSend::find($param['order_id']);

        return self::OrderOperate($orderOperation);
    }

    /**
     * 改变订单价格
     * @param $param
     * @return array
     * @throws AppException
     */
    public static function changeOrderPrice($param)
    {
        $order = OrderChangePrice::find($param['order_id']);
        /**
         * @var $order OrderChangePrice
         */
        if (!isset($order)) {
            throw new AppException('(ID:' . $order->id . ')未找到订单');
        }
        $orderGoodsChangePriceLogs = self::getOrderGoodsChangePriceLogs($param);

        $order->setOrderGoodsChangePriceLogs($orderGoodsChangePriceLogs);//todo
        $order->setOrderChangePriceLog();
        $order->setDispatchChangePrice($param['dispatch_price']);

        return self::OrderOperate($order);
    }

    private static function getOrderGoodsChangePriceLogs($param)
    {
        return collect($param['order_goods'])->map(function ($orderGoodsParams) use ($param) {

            $orderGoodsChangePriceLog = new OrderGoodsChangePriceLog($orderGoodsParams);
            if (!isset($orderGoodsChangePriceLog->belongsToOrderGoods)) {
                throw new AppException('(ID:' . $orderGoodsChangePriceLog->order_goods_id . ')未找到订单商品记录');

            }
            if ($orderGoodsChangePriceLog->belongsToOrderGoods->order_id != $param['order_id']) {
                throw new AppException('(ID:' . $orderGoodsChangePriceLog->order_goods_id . ',' . $param['order_id'] . ')未找到与商品对应的订单');
            }
            //todo 如果不清空,可能会在push时 保存未被更新的订单商品数据,此处需要重新设计
            $orderGoodsChangePriceLog->setRelations([]);
            return $orderGoodsChangePriceLog;
        });
    }

    /**
     * 所有订单商品都是实物 todo 没想好放在哪个类
     * @param Collection $orderGoodsCollect
     * @return bool
     */
    public static function allGoodsIsReal(Collection $orderGoodsCollect)
    {
        return $orderGoodsCollect->contains(function ($orderGoods) {

            return $orderGoods->belongsToGood->isRealGoods();
        });
    }
}