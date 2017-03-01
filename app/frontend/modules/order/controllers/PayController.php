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
use app\frontend\modules\services\order\OrderLogService;
use app\frontend\modules\services\order\PayTypeService;

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
        $plid = OrderLogService::verifyLog(
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

    private function _getPayWays()
    {
        return array(
            'weixin',
            'alipay',
            'app_alipay',
            'app_weixin',
            'unionpay',
            'yunpay',
            'yeepay',
            'paypal',
            'yeepay_wy',
            'credit',
            'cash',
            'storecash'
        );
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

    private function _getOrderSqlCondition()
    {
        if(is_array($this->_getOrderId())){
            $orderids = implode(',', $this->_getOrderId());
            $condition = "id in ({$orderids})";
        }else{
            $condition = "id={$this->_getOrderId()}";
        }
        return $condition;
    }

    //下面支付动作能用到，但是两个处理方式不同。这里只做查询
    private function _getOrderGoods()
    {
        $order_goods = Order::getOrderGoods($this->_getOrderGoodsSqlCondition(), $this->uniacid);
        return $order_goods;
    }

    //公共的地方 pay与complete
    private function _codeBlock()
    {
        $order = $this->_getOrder();
        //验证当前支付方式是否存在
        PayTypeService::verifyPay(
            \YunShop::request()->type,
            $this->_getPayWays()
        );
        //验证用户余额是否足够
        PayTypeService::verifyMemberCredit(
            $this->openid,
            $order
        );
        //获取支付号
        $pay_ordersn = OrderService::getOrderSnGeneral(
            $order,
            $this->_getOrdersnGeneral()
        );
        //获取log,并验证是否为空
        OrderLogService::verifyLogIsEmpty(
            Order::getLog(
                $pay_ordersn,
                \YunShop::app()->uniacid
            )
        );
        //获取order_goods 并验证
        OrderService::verifyOrderGoods(
            $this->_getOrderGoods(),
            \YunShop::app()->uniacid,
            $this->openid
        );
    }


    public function display()
    {
        $order = $this->_getOrder();
        $pay_ways = PayTypeService::getAllPayWay(
            $order,
            $this->openid,
            \YunShop::app()->uniacid
        );
        /*$returnurl = urlencode($this->createMobileUrl('order/pay', array(
            'orderid' => $order['id']
        )));*/
        Url::absoluteApp('sss.sdfasd.dfd',[]);
        $returnurl = '';
        $order_goods = OrderService::getOrderGoods(
            $this->_getOrderGoods(),
            $this->uniacid
        );
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

    //支付
    public function pay()
    {
        //调用公共的代码块
        $this->_codeBlock();
        //支付标题
        $param_title = $this->set()['shop']['name'] . "订单: " . $this->_getOrder()['ordersn'];
        //处理支付handle
        OrderService::handlePay(
            \YunShop::request()->type,
            $this->_getPlId(),
            $this->openid,
            $param_title,
            \YunShop::app(),
            $this->_getOrder(),
            $this->_getOrderSqlCondition()
            );
    }

    public function complete()
    {
        //$verify_set = m('common')->getSetData();
        //$allset = iunserializer($verify_set['plugins']);
        $verify_set = array();
        $allset = array();
        //调用公共的代码块
        $this->_codeBlock();
        OrderService::completeHandlePay(
            \YunShop::request()->type,
            \YunShop::app()->uniacid,
            $this->_getOrderSqlCondition(),
            $this->getOrder(),
            Order::getLog(
                $this->uniacid,
                $this->_getOrdersnGeneral()
            ),
            $this->openid,
            OrderService::getOrderSnGeneral(
                $this->getOrder(),
                $this->_getOrdersnGeneral()
            )
        );
    }
}