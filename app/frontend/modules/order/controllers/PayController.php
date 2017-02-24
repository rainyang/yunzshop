<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/2/24
 * Time: 下午3:42
 */

namespace app\frontend\modules\order\controllers;

use app\common\helpers\Url;
use app\common\models\Order;
use app\frontend\modules\services\order\OrderService;

class PayController
{
    private $set;
    private $openid;
    private $orderid;

    public function __construct()
    {
        //$this->set      = m('common')->getSysset('shop');
        //$this->open_id  = m('user')->getOpenid();
        $this->set     = array('pay', 'shop');
        $this->openid  = 'ud9970b9f87fbc4db2525555bbc899cad';
        $this->orderid = intval(\YunShop::request()->orderid);
        //订单多个的情况下对orderid重新赋值
        $this->orderid = $this->_getOrderId();
    }

    //获取用户open_id
    private function _getOpenId()
    {
        if (!$this->openid) {
            $this->openid = \YunShop::request()->openid;
        }
    }

    //获取订单支付号
    private function _getOrdersnGeneral()
    {
        $ordersn_general = Order::getOrdersnGeneral(
            $this->_getOpenId(),
            \YunShop::request()->uniacid,
            OrderService::verifyOrderId(
                $this->orderid
            )
        );
        return $ordersn_general;
    }

    //获取order
    private function _getOrder()
    {
        //获取订单
        $order = OrderService::getOrder(
            Order::getOrders(
                $this->_getOrdersnGeneral(),
                \YunShop::request()->uniacid,
                $this->_getOpenId()
            ),
            $this->_getOrdersnGeneral()
        );
        return $order;
    }

    private function _getOrderId()
    {
        $orderid = OrderService::getOrderId(
            Order::getOrders(
                $this->_getOrdersnGeneral(),
                \YunShop::request()->uniacid,
                $this->_getOpenId()
            ),
            $this->_getOrdersnGeneral()
        );
        return $orderid;
    }

    //获取log ID
    private function _getPlId()
    {
        $plid = OrderService::verifyLog(
            Order::getLog(
                $this->uniacid,
                $this->_getOrdersnGeneral()
            ),
            $this->uniacid,
            $this->openid,
            $this->_getOrdersnGeneral(),
            $this->getOrder()['price'],
            0
        );
        return $plid;
    }

    private function _getOrderGoodsSqlCondition()
    {
        if(is_array($this->_getOrderId())){
            $orderids = implode(',', $this->_getOrderId());
            $condition = "og.orderid in ({$orderids})";
        }else{
            $condition = "og.orderid={$this->_getOrderId()}";
        }
        return $condition;
    }

    //下面支付动作能用到，但是两个处理方式不同。这里只做查询
    private function _getOrderGoods()
    {
        $order_goods = Order::getOrderGoods($this->_getOrderGoodsSqlCondition(), $this->uniacid);
        return $order_goods;
    }

    public function display()
    {
        $order = $this->_getOrder();
        $pay_ways = OrderService::getAllPayWay($order, $this->openid, \YunShop::app()->uniacid);
        /*$returnurl = urlencode($this->createMobileUrl('order/pay', array(
            'orderid' => $order['id']
        )));*/
        $returnurl = '';
        $order_goods = OrderService::getOrderGoods($this->_getOrderGoods(), $this->uniacid);
        return show_json(1, array(
            'order' => $order,
            'set' => $this->set,
            'credit' => $pay_ways['credit'],
            'wechat' => $pay_ways['wechat'],
            'alipay' => $pay_ways['alipay'],
            'app_wechat' => $pay_ways['app_wechat'],
            'app_alipay' => $pay_ways['app_alipay'],
            'unionpay' => $pay_ways['unionpay'],
            //'yunpay' => $pay_ways['yunpay'],
            //'cash' => $cash,
            //'storecash' => $storecash,
            'yeepay' => $pay_ways['yeepay'],
            //'gaohuitong' => $gaohuitong,
            'paypal' => $pay_ways['paypal'],
            'isweixin' => is_weixin(),
            //'currentcredit' => $currentcredit,
            'returnurl' => $returnurl,
            'goods'=>$order_goods
        ));
    }
}