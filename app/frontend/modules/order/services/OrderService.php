<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/24
 * Time: 下午4:35
 */

namespace app\frontend\modules\order\services;

use app\common\events\discount\OnDiscountInfoDisplayEvent;
use app\common\events\dispatch\OnDispatchTypeInfoDisplayEvent;
use app\common\events\order\OnPreGenerateOrderCreatingEvent;
use app\common\exceptions\AppException;
use app\common\models\Order;

use app\common\models\order\OrderGoodsChangePriceLog;
use app\common\models\UniAccount;
use \app\frontend\models\MemberCart;
use app\frontend\modules\member\services\MemberService;
use app\frontend\modules\order\models\PreOrder;
use app\frontend\modules\order\services\behavior\OrderCancelPay;
use app\frontend\modules\order\services\behavior\OrderCancelSend;
use app\frontend\modules\order\services\behavior\OrderChangePrice;
use app\frontend\modules\order\services\behavior\OrderClose;
use app\frontend\modules\order\services\behavior\OrderDelete;
use app\frontend\modules\order\services\behavior\OrderOperation;
use app\frontend\modules\order\services\behavior\OrderPay;
use app\frontend\modules\order\services\behavior\OrderReceive;
use app\frontend\modules\order\services\behavior\OrderSend;
use app\frontend\modules\shop\services\ShopService;
use Carbon\Carbon;
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
        // todo 这里为什么要toArray
        $result->put('order', $order->toArray());
        $result->put('discount', self::getDiscountEventData($order));
        $result->put('dispatch', self::getDispatchEventData($order));

        if (!$result->has('supplier')) {
            $result->put('supplier', ['username' => array_get(\Setting::get('shop'), 'name', '自营'), 'id' => 0]);
        }


        return $result;
    }

    /**
     * 获取优惠信息
     * @param $orderModel
     * @return array
     */
    private static function getDiscountEventData($orderModel)
    {
        $event = new OnDiscountInfoDisplayEvent($orderModel);
        event($event);

        return collect($event->getMap());
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
    public static function getOrderGoods(Collection $memberCarts)
    {
        if ($memberCarts->isEmpty()) {
            throw new AppException("(" . $memberCarts->goods_id . ")未找到订单商品");
        }
        $result = $memberCarts->map(function ($memberCart) {
            if (!($memberCart instanceof MemberCart)) {
                throw new \Exception("请传入" . MemberCart::class . "的实例");
            }
            /**
             * @var $memberCart MemberCart
             */

            $data = [
                'goods_id' => (int)$memberCart->goods_id,
                'goods_option_id' => (int)$memberCart->option_id,
                'total' => (int)$memberCart->total,
            ];
            return app('OrderManager')->make('PreOrderGoods', $data);
        });

        return $result;
    }


    /**
     * 根据购物车记录,获取订单信息
     * @param Collection $memberCarts
     * @param null $member
     * @return bool|mixed
     * @throws AppException
     */
    public static function createOrderByMemberCarts(Collection $memberCarts, $member = null)
    {
        if (!isset($member)) {
            //默认使用当前登录用户下单
            $member = MemberService::getCurrentMemberModel();
        }
        if (!isset($member)) {
            throw new AppException('用户登录状态过期');
        }

        if ($memberCarts->isEmpty()) {
            return false;
        }

        $shop = ShopService::getCurrentShopModel();

        $orderGoodsArr = OrderService::getOrderGoods($memberCarts);
        $order = app('OrderManager')->make('PreOrder', ['uid' => $member->uid, 'uniacid' => $shop->uniacid]);

        event(new OnPreGenerateOrderCreatingEvent($order));
        $order->setOrderGoods($orderGoodsArr);
        /**
         * @var PreOrder $order
         */
        $order->_init();
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

    /**
     * 获取支付流水号
     * @return string
     */
    public static function createPaySN()
    {
        $paySN = createNo('PN', true);
        while (1) {
            if (!\app\common\models\OrderPay::where('pay_sn', $paySN)->first()) {
                break;
            }
            $paySN = createNo('PN', true);
        }
        return $paySN;
    }

    /**
     * 订单操作类 todo 以前不了解抛异常机制,所有先check.现在可以移除check
     * {@inheritdoc}
     */
    private static function OrderOperate(OrderOperation $orderOperation)
    {
        if (!isset($orderOperation)) {
            throw new AppException('未找到该订单');
        }

        DB::transaction(function () use ($orderOperation) {
            $orderOperation->check();
            $orderOperation->execute();

        });
        return $orderOperation->getMessage();
    }

    /**
     * 取消付款
     * @param $param
     * @return string
     */
    public static function orderCancelPay($param)
    {
        $orderOperation = OrderCancelPay::find($param['order_id']);

        return self::OrderOperate($orderOperation);
    }

    /**
     * 取消发货
     * @param $param
     * @return string
     */
    public static function orderCancelSend($param)
    {
        $orderOperation = OrderCancelSend::find($param['order_id']);

        return self::OrderOperate($orderOperation);
    }

    /**
     * 关闭订单
     * @param $param
     * @return string
     */
    public static function orderClose($param)
    {
        $orderOperation = OrderClose::find($param['order_id']);

        return self::OrderOperate($orderOperation);
    }

    /**
     * 用户删除(隐藏)订单
     * @param $param
     * @return string
     */
    public static function orderDelete($param)
    {
        $orderOperation = OrderDelete::find($param['order_id']);

        return self::OrderOperate($orderOperation);
    }

    /**
     * 根据流水号合并支付
     * @param array $param
     * @throws AppException
     */
    public static function ordersPay(array $param)
    {
        $orderPay = \app\common\models\OrderPay::find($param['order_pay_id']);
        if (!isset($orderPay)) {
            throw new AppException('支付流水记录不存在');
        }
        $orders = Order::whereIn('id', $orderPay->order_ids)->get();
        if ($orders->isEmpty()) {
            throw new AppException('(ID:' . $orderPay->id . ')未找到订单流水号对应订单');
        }
        DB::transaction(function () use ($orderPay, $orders, $param) {
            $orderPay->status = 1;
            if (isset($param['pay_type_id'])) {
                $orderPay->pay_type_id = $param['pay_type_id'];
            }
            $orderPay->save();
            $orders->each(function ($order) use ($param) {
                if (!OrderService::orderPay(['order_id' => $order->id, 'order_pay_id' => $param['order_pay_id'], 'pay_type_id' => $param['pay_type_id']])) {
                    throw new AppException('订单状态改变失败,请联系客服');
                }
            });
        });
    }

    /**
     * 后台支付订单
     * @param array $param
     * @return string
     */

    public static function orderPay(array $param)
    {
        /**
         * @var OrderOperation $orderOperation
         */
        $orderOperation = OrderPay::find($param['order_id']);
        if (isset($param['pay_type_id'])) {
            $orderOperation->pay_type_id = $param['pay_type_id'];
        }
        $orderOperation->order_pay_id = (int)$param['order_pay_id'];
//        if (isset($param['order_pay_id'])) {
//            if (isset($orderOperation->hasOneOrderPay)) {
//                if (in_array($param['order_id'], $orderOperation->hasOneOrderPay->order_ids)) {
//                    $orderOperation->order_pay_id = $param['order_pay_id'];
//                }
//            }
//        }
        $result = self::OrderOperate($orderOperation);
        if ($orderOperation->isVirtual()) {
            // 虚拟物品付款后直接完成
            self::orderSend(['order_id' => $orderOperation->id]);
            $result = self::orderReceive(['order_id' => $orderOperation->id]);
        } elseif (isset($orderOperation->hasOneDispatchType) && !$orderOperation->hasOneDispatchType->needSend()) {
            // 不需要发货的物品直接改为待收货
            self::orderSend(['order_id' => $orderOperation->id]);
        }
        return $result;
    }

    /**
     * 收货
     * @param $param
     * @return string
     */
    public static function orderReceive($param)
    {
        $orderOperation = OrderReceive::find($param['order_id']);

        return self::OrderOperate($orderOperation);
    }

    /**
     * 发货
     * @param $param
     * @return string
     */
    public static function orderSend($param)
    {
        $orderOperation = OrderSend::find($param['order_id']);

        return self::OrderOperate($orderOperation);
    }

    /**
     * 改变订单价格
     * @param $param
     * @return string
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

    /**
     * 订单改价记录
     * {@inheritdoc}
     */
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
     * 自动收货
     * {@inheritdoc}
     */
    public static function autoReceive($uniacid)
    {
        \YunShop::app()->uniacid = $uniacid;
        \Setting::$uniqueAccountId = $uniacid;
        $days = (int)\Setting::get('shop.trade.receive');

        if (!$days) {
            return;
        }
        $orders = \app\backend\modules\order\models\Order::waitReceive()->where('send_time', '<', (int)Carbon::now()->addDays(-$days)->timestamp)->normal()->get();
        if (!$orders->isEmpty()) {
            $orders->each(function ($order) {
                try{
                    OrderService::orderReceive(['order_id' => $order->id]);
                }catch (\Exception $e){

                }
            });
        }
    }

    /**
     * 自动关闭订单
     * {@inheritdoc}
     */
    public static function autoClose()
    {
        $days = (int)\Setting::get('shop.trade.close_order_days');
        if (!$days) {
            return;
        }
        $orders = \app\backend\modules\order\models\Order::waitPay()->where('create_time', '<', (int)Carbon::now()->addDays(-\Setting::get('shop.trade.close_order_days'))->timestamp)->normal()->get();
        if (!$orders->isEmpty()) {
            $orders->each(function ($order) {
                //dd($order->send_time);
                OrderService::orderClose(['order_id' => $order->id]);
            });
        }
    }
}