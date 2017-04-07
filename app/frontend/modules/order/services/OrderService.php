<?php

/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/2/24
 * Time: 下午4:35
 */

namespace app\frontend\modules\order\services;

use app\common\models\Order;
use app\common\models\Member;

use app\frontend\modules\goods\services\models\GoodsModel;
use app\frontend\modules\member\models\MemberCart;
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
use app\frontend\modules\shop\services\models\ShopModel;

class OrderService
{

    /**
     * 获取订单商品对象数组
     * @param $memberCarts
     * @return array
     */
    public static function getOrderGoodsModels(array $memberCarts){
        $result = [];
        foreach ($memberCarts as $memberCart) {
            if(!($memberCart instanceof MemberCart)){
                throw new \Exception("请传入".MemberCart::class."的实例");
            }
            /**
             * @var $memberCart MemberCart
             */
            $orderGoodsModel = new PreGeneratedOrderGoodsModel($memberCart->toArray());
            $result[] = $orderGoodsModel;
        }
        return $result;
    }

    /**
     * 获取订单号
     * @return string
     */
    public static function createOrderSN(){
        return 'sn'.time();//m('common')->createNO('order', 'ordersn', 'SH');
    }
    private static function OrderOperate(OrderOperation $OrderOperate){
        if(!$OrderOperate->enable()){
            return [false,$OrderOperate->getMessage()];
        }
        if(!$OrderOperate->execute()){
            return [false,$OrderOperate->getMessage()];
        }
        return [true,$OrderOperate->getMessage()];
    }
    /**
     * 取消付款
     * @param $param
     * @return array
     */
    public static function orderCancelPay($param){
        $order_model = Order::find($param['order_id']);

        $OrderOperation = new OrderCancelPay($order_model);
        return self::OrderOperate($OrderOperation);
    }
    /**
     * 取消发货
     * @param $param
     * @return array
     */
    public static function orderCancelSend($param){
        $order_model = Order::find($param['order_id']);

        $OrderOperation = new OrderCancelSend($order_model);
        return self::OrderOperate($OrderOperation);
    }
    /**
     * 关闭订单
     * @param $param
     * @return array
     */
    public static function orderClose($param){
        $order_model = Order::find($param['order_id']);

        $OrderOperation = new OrderClose($order_model);
        return self::OrderOperate($OrderOperation);
    }
    /**
     * 用户删除(隐藏)订单
     * @param $param
     * @return array
     */
    public static function orderDelete($param){
        $order_model = Order::find($param['order_id']);

        $OrderOperation = new OrderDelete($order_model);
        return self::OrderOperate($OrderOperation);
    }

    /**
     * 支付订单
     * @param array $param
     * @return array
     */

    public static function orderPay(array $param){
        $order_model = Order::find($param['order_id']);
        $OrderOperation = new OrderPay($order_model);
        return self::OrderOperate($OrderOperation);
    }
    /**
     * 收货
     * @param $param
     * @return array
     */
    public static function orderReceive($param){
        $order_model = Order::find($param['order_id']);

        $OrderOperation = new OrderReceive($order_model);
        return self::OrderOperate($OrderOperation);
    }
    /**
     * 发货
     * @param $param
     * @return array
     */
    public static function orderSend($param){
        $order_model = Order::find($param['order_id']);

        $OrderOperation = new OrderSend($order_model);
        return self::OrderOperate($OrderOperation);
    }
    /**
     * 改变订单价格
     * @param $param
     * @return array
     */
    public static function changeOrderPrice($param){
        $order_model = Order::find($param['order_id']);

        $OrderOperation = new OrderChangePrice($order_model);
        return self::OrderOperate($OrderOperation);
    }
}