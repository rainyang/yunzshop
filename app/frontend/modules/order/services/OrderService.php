<?php

/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/2/24
 * Time: 下午4:35
 */

namespace app\frontend\modules\services\order;

use app\common\models\Order;

class OrderService
{
    public static function getOrder($orders, $ordersn_general)
    {
        //合并订单号，订单数大于1，执行合并付款
        if (count($orders) > 1) {
            $order = array();
            $order['ordersn'] = $ordersn_general;
            $orderid = array();
            foreach ($orders as $key => $val) {
                $order['price']           += $val['price'];
                $order['deductcredit2']   += $val['deductcredit2'];
                $order['ordersn2']   	  += $val['ordersn2'];
                $orderid[]				   = $val['id'];
            }
            $order['status']	    = $val['status'];
            $order['cash']		    = $val['cash'];
            $order['openid']		= $val['openid'];
            $order['pay_ordersn']   = $val['pay_ordersn'];
        } else {
            $order = $orders[0];
        }
        if (empty($orders)) {
            return show_json(0, '订单未找到!');
        }
        if ($order['status'] == -1) {
            return show_json(-1, '订单已关闭, 无法付款!');
        } elseif ($order['status'] >= 1) {
            return show_json(-1, '订单已付款, 无需重复支付!');
        }
        return $order;
    }

    public static function getOrderId($orders)
    {
        if (count($orders) > 1) {
            $orderid = array();
            foreach ($orders as $key => $val) {
                $orderid[] = $val['id'];
            }
        } else {
            $orderid = $orders[0]['id'];
        }
        return $orderid;
    }

    public static function verifyOrderId($orderid)
    {
        if (!empty($orderid)) {
            return $orderid;
        }
        return show_json(0, '参数错误');
    }

    public static function verifyLog($log, $uniacid, $openid, $ordersn_general, $price, $status)
    {
        if (!empty($log) && $log['status'] != '0') {
            return show_json(-1, '订单已支付, 无需重复支付!');
        }
        if (!empty($log) && $log['status'] == '0') {
            Order::deleteLog($log['plid']);
            $log = null;
        }
        $plid = $log['plid'];
        if (empty($log)) {
            $log = array(
                'uniacid' => $uniacid,
                'openid' => $openid,
                'module' => "sz_yi",
                'tid' => $ordersn_general,
                'fee' => $price,
                'status' => $status
            );
            $plid = Order::insertLog($log);
        }
        return $plid;
    }

    //获取所有的支付方式
    public static function getAllPayWay($order, $openid, $uniacid)
    {
        //$set      = m('common')->getSysset();
        //load()->model('payment');
        //$setting = uni_setting($_W['uniacid'], array('payment'));
        $set = array();
        $setting = array();
        $pay_ways = array();

        //余额支付
        $credit        = array(
            'success' => false
        );
        if (isset($set['pay']) && $set['pay']['credit'] == 1) {
            if ($order['deductcredit2'] <= 0) {
                $credit = array(
                    'success' => true,
                    //'current' => m('member')->getCredit($openid, 'credit2')
                    'current' => '100000'
                );
            }
        }
        $pay_ways[] = $credit;

        //app阿里支付
        $app_alipay = array(
            'success' => false
        );
        if (isset($set['pay']) && $set['pay']['app_alipay'] == 1) {
            $app_alipay['success'] = true;
        }
        $pay_ways[] = $app_alipay;

        //app微信支付
        $app_wechat = array(
            'success' => false
        );
        if (isset($set['pay']) && $set['pay']['app_weixin'] == 1) {
            $app_wechat['success'] = true;
        }
        $pay_ways[] = $app_wechat;

        //微信支付
        $wechat  = array(
            'success' => false,
            'qrcode' => false
        );
        $jie = $set['pay']['weixin_jie'];
        if (is_weixin()) {
            if (isset($set['pay']) && ($set['pay']['weixin'] == 1) && ($jie != 1)) {
                if (is_array($setting['payment']['wechat']) && $setting['payment']['wechat']['switch']) {
                    $wechat['success'] = true;
                    $wechat['weixin'] = true;
                    $wechat['weixin_jie'] = false;
                }
            }
        }
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            if ((isset($set['pay']) && ($set['pay']['weixin_jie'] == 1) && !$wechat['success']) || ($jie == 1)) {
                $wechat['success'] = true;
                $wechat['weixin_jie'] = true;
                $wechat['weixin'] = false;
            }
        }
        $wechat['jie'] = $jie;
        //扫码
        if (!isMobile() && isset($set['pay']) && $set['pay']['weixin'] == 1) {
            if (isset($set['pay']) && $set['pay']['weixin'] == 1) {
                if (is_array($setting['payment']['wechat']) && $setting['payment']['wechat']['switch']) {
                    $wechat['qrcode'] = true;
                }
            }
        }
        $pay_ways[] = $app_alipay;

        //阿里支付
        $alipay = array(
            'success' => false
        );
        if (isset($set['pay']) && $set['pay']['alipay'] == 1) {
            if (is_array($setting['payment']['alipay']) && $setting['payment']['alipay']['switch']) {
                $alipay['success'] = true;
            }
        }
        $pay_ways[] = $app_alipay;

        //银联支付
        $unionpay = array(
            'success' => false
        );
        if (isset($set['pay']) && $set['pay']['unionpay'] == 1) {
            if (is_array($setting['payment']['unionpay']) && $setting['payment']['unionpay']['switch']) {
                $unionpay['success'] = true;
            }
        }
        $pay_ways[] = $app_alipay;

        //易宝支付
        $yeepay = array(
            'success' => false
        );
        if (isset($set['pay']) && $set['pay']['yeepay'] == 1) {
            $yeepay['success'] = true;
        }
        $pay_ways[] = $app_alipay;

        //paypal支付
        $paypal = array(
            'success' => false
        );
        if (isset($set['pay']) && $set['pay']['paypalstatus'] == 1){
            $paypal['success'] = true;
        }
        $pay_ways[] = $app_alipay;

        return $pay_ways;
    }

    //处理订单商品
    public function getOrderGoods($order_goods, $uniacid)
    {
        foreach ($order_goods as $key => &$value) {
            if (!empty($value['optionid'])) {
                $option = pdo_fetch("SELECT id, title, marketprice, goodssn, productsn, stock, virtual, weight FROM " .
                    tablename("sz_yi_goods_option") .
                    " WHERE id = :id AND goodsid = :goodsid AND uniacid = :uniacid  limit 1",
                    array(
                        ":uniacid" => $uniacid,
                        ":goodsid" => $value['goodsid'],
                        ":id" => $value['optionid']
                    )
                );
                if (!empty($option)) {
                    $value["optionid"]    = $value['optionid'];
                    $value["optiontitle"] = $option["title"];
                    $value["marketprice"] = $option["marketprice"];
                    if (!empty($option["weight"])) {
                        $value["weight"] = $option["weight"];
                    }
                }
            }
        }
        unset($value);
        $order_goods = set_medias($order_goods, 'thumb');
        return $order_goods;
    }
}