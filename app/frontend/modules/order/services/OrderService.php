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

    //验证log是否为空
    public static function verifyLogIsEmpty($log)
    {
        if (empty($log)) {
            return show_json(0, '支付出错,请重试!');
        }
    }

    //验证log 并返回plid
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
    public static function getOrderGoods($order_goods, $uniacid)
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

    //验证当前支付方式
    public static function verifyPay($pay_type, $pay_way)
    {
        if (!in_array($pay_type, $pay_way)) {
            return show_json(0, '未找到支付方式');
        }
    }

    //验证用户余额是否足够
    public static function verifyMemberCredit($openid, $order)
    {
        //$member = m('member')->getInfo($openid);
        $member = array();
        if($member['credit2'] < $order['deductcredit2'] && $order['deductcredit2'] > 0){
            return show_json(0, '余额不足，请充值后在试！');
        }
    }

    public static function getOrderSnGeneral($order, $ordersn_general)
    {
        $pay_ordersn = $order['pay_ordersn'] ? $order['pay_ordersn'] : $ordersn_general;
        return $pay_ordersn;
    }

    //验证order_goods
    public static function verifyOrderGoods($order_goods, $uniacid, $openid)
    {
        //$member = m('member')->getInfo($openid);
        $member = array();
        foreach ($order_goods as $data) {
            if (empty($data['status']) || !empty($data['deleted'])) {
                return show_json(-1, $data['title'] . '<br/> 已下架!');
            }
            if ($data['maxbuy'] > 0) {
                if ($data['buycount'] > $data['maxbuy']) {
                    return show_json(-1, $data['title'] . '<br/> 一次限购 ' . $data['maxbuy'] . "!");
                }
            }
            if ($data['usermaxbuy'] > 0) {
                $order_goods_count = Order::getBuyCount($data['goodsid'], $uniacid, $openid);
                if ($order_goods_count >= $data['usermaxbuy']) {
                    return show_json(-1, $data['title'] . '<br/> 最多限购 ' . $data['usermaxbuy'] . "!");
                }
            }
            if ($data['istime'] == 1) {
                if (time() < $data['timestart']) {
                    return show_json(-1, $data['title'] . '<br/> 限购时间未到!');
                }
                if (time() > $data['timeend']) {
                    return show_json(-1, $data['title'] . '<br/> 限购时间已过!');
                }
            }
            if ($data['buylevels'] != '') {
                $buylevels = explode(',', $data['buylevels']);
                if (!in_array($member['level'], $buylevels)) {
                    return show_json(-1, '您的会员等级无法购买<br/>' . $data['title'] . '!');
                }
            }
            if ($data['buygroups'] != '') {
                $buygroups = explode(',', $data['buygroups']);
                if (!in_array($member['groupid'], $buygroups)) {
                    return show_json(-1, '您所在会员组无法购买<br/>' . $data['title'] . '!');
                }
            }
            if($data['totalcnf']>0) {
                if (!empty($data['optionid'])) {
                    //等商品的接口
                    $option = pdo_fetch('select id,title,marketprice,goodssn,productsn,stock,virtual from ' . tablename('sz_yi_goods_option') . ' where id=:id and goodsid=:goodsid and uniacid=:uniacid  limit 1', array(
                        ':uniacid' => $uniacid,
                        ':goodsid' => $data['goodsid'],
                        ':id' => $data['optionid']
                    ));
                    if (!empty($option)) {
                        if ($option['stock'] != -1) {
                            if (empty($option['stock'])  OR ($option['buycount'] > $data['stock'])) {
                                return show_json(-1, $data['title'] . "<br/>" . $option['title'] . " 库存不足!");
                            }
                        }
                    }
                } else {
                    if ($data['stock'] != -1) {
                        if (empty($data['stock']) OR ($data['buycount'] > $data['stock'])) {
                            return show_json(-1, $data['title'] . "<br/>库存不足!");
                        }
                    }
                }
            }
        }
    }

    //处理支付
    public static function handlePay($pay_type, $plid, $openid, $param_title, $order, $condition)
    {
        //set 等接口
        if ($pay_type == 'weixin') {
            if (!empty($set['pay']['weixin']) || !empty($set['pay']['weixin_jie'])) {
            }else{
                return show_json(0, '未开启微信支付!');
            }
            $wechat        = array(
                'success' => false
            );
            $params        = array();
            $params['tid'] = $plid;
            if (!empty($order['ordersn2'])) {
                $var = sprintf("%02d", $order['ordersn2']);
                $params['tid'] .= "GJ" . $var;
            }
            $params['user']  = $openid;
            $params['fee']   = $order['price'];
            $params['title'] = $param_title;
            //等接口
            load()->model('payment');
            $setting = uni_setting(\YunShop::app()->uniacid, array(
                'payment'
            ));
            //微信下
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
                if (is_array($setting['payment'])) {
                    $options           = $setting['payment']['wechat'];
                    if (is_weixin()) {
                        if(empty($set['pay']['weixin_jie'])){
                            $options['appid'] = \YunShop::app()->account['key'];
                            $options['secret'] = \YunShop::app()->account['secret'];
                            //////
                            $wechat            = m('common')->wechat_build($params, $options, 0);
                            if (!is_error($wechat)) {
                                $wechat['success'] = true;
                            } else {
                                return show_json(0, $wechat['message']);
                            }
                        }
                    }
                    if(!empty($set['pay']['weixin_jie'])){
                        $options['appid'] = $set['pay']['weixin_jie_appid'];
                        $options['mchid'] = $set['pay']['weixin_jie_mchid'];
                        $options['apikey'] = $set['pay']['weixin_jie_apikey'];
                        //////////
                        $wechat = m('common')->wechat_native_build($params, $options, 0);
                        if (!is_error($wechat)) {
                            $wechat['success'] = true;
                            $wechat['weixin_jie'] = true;
                        }
                    }
                }
                if (!$wechat['success']) {
                    return show_json(0, '微信支付参数错误!');
                }
                Order::payUpdateOrder(
                    $condition,
                    \YunShop::app()->uniacid,
                    '21'
                );
                show_json(1, array(
                    'wechat' => $wechat
                ));
            } else if (is_app_api()) {//新版app原生支付
                $options           = $setting['payment']['wechat'];
                $pay = $setting['payment'];
                $options['mchid'] = $pay['wx_native']['wx_mcid'];
                $options['appid'] = $pay['wx_native']['wx_appid'];
                $options['secret'] = $pay['wx_native']['wx_secret'];
                $options['signkey'] = $pay['wx_native']['signkey'];

                $params['trade_type'] = 'APP';
                //////////////////
                $wechat            = m('common')->wechat_build($params, $options, 0);
                if (!is_error($wechat)) {
                    $wechat['success'] = true;
                } else {
                    return show_json(0, $wechat['message']);
                }
                if (!$wechat['success']) {
                    return show_json(0, '微信支付参数错误!');
                }
            } else {   //PC端微信扫码pay
                if (is_array($setting['payment'])) {
                    $params['trade_type']  = 'NATIVE';
                    $options           = $setting['payment']['wechat'];
                    $options['appid']  = \YunShop::app()->account['key'];
                    $options['secret'] = \YunShop::app()->account['secret'];
                    ////////////////
                    $wechat            = m('common')->wechat_build($params, $options, 0);
                    if (!is_error($wechat)) {
                        $wechat['success'] = true;
                        /////////////////////
                        $wechat['code_url'] = m('qrcode')->createWechatQrcode($wechat['code_url']);
                    } else {
                        return show_json(0, $wechat['message']);
                    }
                }
            }
            Order::payUpdateOrder(
                $condition,
                \YunShop::app()->uniacid,
                '21'
            );
            return show_json(1, array(
                'wechat' => $wechat
            ));
        } else if ($pay_type == 'alipay') {
            Order::payUpdateOrder(
                $condition,
                \YunShop::app()->uniacid,
                '22'
            );
            return show_json(1);
        } else if ($pay_type == 'yunpay') {
            Order::payUpdateOrder(
                $condition,
                \YunShop::app()->uniacid,
                '24'
            );
            return show_json(1);
        } else if ($pay_type == 'yeepay') {
            Order::payUpdateOrder(
                $condition,
                \YunShop::app()->uniacid,
                '25'
            );
            return show_json(1);
        } else if ($pay_type == 'yeepay_wy') {
            Order::payUpdateOrder(
                $condition,
                \YunShop::app()->uniacid,
                '26'
            );
            return show_json(1);
        } else if ($pay_type == 'paypal') {
            Order::payUpdateOrder(
                $condition,
                \YunShop::app()->uniacid,
                '29'
            );
            return show_json(1);
        }
    }

    //
    public static function completeHandlePay($pay_type, $uniacid, $condition, $order, $log, $openid, $pay_ordersn)
    {
        //set 等接口
        $set = array();
        if ($pay_type == 'cash') {
            if (!$set['pay']['cash']) {
                return show_json(0, '当前支付方式未开启,请重试!');
            }
            Order::payUpdateOrder(
                $condition,
                \YunShop::app()->uniacid,
                '3'
            );
            $ret            = array();
            $ret['result']  = 'success';
            $ret['type']    = 'cash';
            $ret['from']    = 'return';
            $ret['tid']     = $log['tid'];
            $ret['user']    = $order['openid'];
            $ret['fee']     = $order['price'];
            $ret['weid']    = $uniacid;
            $ret['uniacid'] = $uniacid;
            //////////////////
            $pay_result      = $this->payResult($ret);
            $set = m('common')->getSysset();
            //互亿无线
            if (!empty($pay_result['verifycode'])) {
                if($pset['sms']['type'] == 1){
                    if($pay_result['verifycode']['SubmitResult']['code'] == 2 || $allset['verify']['sendcode'] == 0 || empty($order['isverify'])){
                        return show_json(1, $pay_result);
                    }
                    else{
                        return show_json(0, $pay_result['verifycode']['SubmitResult']['msg']);
                    }
                }
                else{
                    if(isset($pay_result['verifycode']['result']['success']) || $allset['verify']['sendcode'] == 0 || empty($order['isverify'])){
                        return show_json(1, $pay_result);
                    }
                    else{
                        return show_json(0, $pay_result['verifycode']['msg']);
                    }
                }
            } else {
                return show_json(1, $pay_result);
            }
        }
        $ps          = array();
        $ps['tid']   = $log['tid'];
        $ps['user']  = $openid;
        $ps['fee']   = $log['fee'];
        $ps['title'] = $log['title'];
        if ($pay_type == 'storecash') {
            if (!$set['pay']['cash']) {
                return show_json(0, '当前支付方式未开启,请重试!');
            }
            Order::payUpdateOrder(
                $condition,
                \YunShop::app()->uniacid,
                '4'
            );
            $ret            = array();
            $ret['result']  = 'success';
            $ret['type']    = 'storecash';
            $ret['from']    = 'return';
            $ret['tid']     = $log['tid'];
            $ret['user']    = $order['openid'];
            $ret['fee']     = $order['price'];
            $ret['weid']    = $uniacid;
            $ret['uniacid'] = $uniacid;
            $pay_result      = $this->payResult($ret);
            $set = m('common')->getSysset();
            //互亿无线
            if (!empty($pay_result['verifycode'])) {
                if($pset['sms']['type'] == 1){
                    if($pay_result['verifycode']['SubmitResult']['code'] == 2 || $allset['verify']['sendcode'] == 0 || empty($order['isverify'])){
                        return show_json(1, $pay_result);
                    }
                    else{
                        return show_json(0, $pay_result['verifycode']['SubmitResult']['msg']);
                    }
                }
                else{
                    if(isset($pay_result['verifycode']['result']['success']) || $allset['verify']['sendcode'] == 0 || empty($order['isverify'])){
                        return show_json(1, $pay_result);
                    }
                    else{
                        return show_json(0, $pay_result['verifycode']['msg']);
                    }
                }
            } else {
                return show_json(1, $pay_result);
            }
        }
        $ps          = array();
        $ps['tid']   = $log['tid'];
        $ps['user']  = $openid;
        $ps['fee']   = $log['fee'];
        $ps['title'] = $log['title'];
        if ($pay_type == 'credit') {
            if (!$set['pay']['credit']) {
                return show_json(0, '余额支付未开启！');
            }
            $credits = m('member')->getCredit($openid, 'credit2');
            if ($credits < $ps['fee']) {
                return show_json(0, "余额不足,请充值");
            }
            $fee    = floatval($ps['fee']);
            $result = m('member')->setCredit($openid, 'credit2', -$fee, array(
                $_W['member']['uid'],
                '消费' . $setting['creditbehaviors']['currency'] . ':' . $fee
            ));
            if (is_error($result)) {
                return show_json(0, $result['message']);
            }
            $record           = array();
            $record['status'] = '1';
            $record['type']   = 'cash';
            pdo_update('core_paylog', $record, array(
                'plid' => $log['plid']
            ));
            Order::payUpdateOrder(
                $condition,
                \YunShop::app()->uniacid,
                '1'
            );
            $ret            = array();
            $ret['result']  = 'success';
            $ret['type']    = $log['type'];
            $ret['from']    = 'return';
            $ret['tid']     = $log['tid'];
            $ret['user']    = $log['openid'];
            $ret['fee']     = $log['fee'];
            $ret['weid']    = $log['weid'];
            $ret['uniacid'] = $log['uniacid'];
            $pay_result = $this->payResult($ret);
            //互亿无线
            if (!empty($pay_result['verifycode'])) {
                if($pset['sms']['type'] == 1){
                    if($pay_result['verifycode']['SubmitResult']['code'] == 2 || $allset['verify']['sendcode'] == 0 || empty($order['isverify'])){
                        return show_json(1, $pay_result);
                    }
                    else{
                        return show_json(0, $pay_result['verifycode']['SubmitResult']['msg']);
                    }
                }
                else{
                    if(isset($pay_result['verifycode']['result']['success']) || $allset['verify']['sendcode'] == 0 || empty($order['isverify'])){
                        return show_json(1, $pay_result);
                    }
                    else{
                        return show_json(0, $pay_result['verifycode']['msg']);
                    }
                }
            } else {
                return show_json(1, $pay_result);
            }

        } else if ($pay_type == 'weixin') {
            $ordersn = $pay_ordersn;
            if (!empty($order['ordersn2'])) {
                $ordersn .= "GJ" . sprintf("%02d", $order['ordersn2']);
            }
            $payquery = m('finance')->isWeixinPay($ordersn);
            if (!is_error($payquery)) {
                $record           = array();
                $record['status'] = '1';
                $record['type']   = 'wechat';
                pdo_update('core_paylog', $record, array(
                    'plid' => $log['plid']
                ));
                $ret            = array();
                $ret['result']  = 'success';
                $ret['type']    = 'wechat';
                $ret['from']    = 'return';
                $ret['tid']     = $log['tid'];
                $ret['user']    = $log['openid'];
                $ret['fee']     = $log['fee'];
                $ret['weid']    = $log['weid'];
                $ret['uniacid'] = $log['uniacid'];
                $ret['deduct']  = intval(\YunShop::request()['deduct']) == 1;
                if(!empty($order['pay_ordersn']) && empty($order['isverify'])){
                    $price = $order['price'];
                    $order = pdo_fetch("select * from " . tablename('sz_yi_order') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1', array(
                        ':id' => $order['id'],
                        ':uniacid' => $uniacid,
                        ':openid' => $openid
                    ));
                    $order['price'] = $price;
                    $address = false;
                    if (empty($order['dispatchtype'])) {
                        $address = pdo_fetch('select realname,mobile,address from ' . tablename('sz_yi_member_address') . ' where id=:id limit 1', array(
                            ':id' => $order['addressid']
                        ));
                    }
                    $carrier = false;
                    if ($order['dispatchtype'] == 1 || $order['isvirtual'] == 1) {
                        $carrier = unserialize($order['carrier']);
                    }
                    $pay_result = array(
                        'result' => 'success',
                        'order' => $order,
                        'address' => $address,
                        'carrier' => $carrier,
                        'virtual' => $order['virtual']
                        //'goods'=> $orderdetail  这个值没有
                    );
                }else{
                    $pay_result     = $this->payResult($ret);
                }
                $pay_result['time'] = time();
                show_json(1, $pay_result);
                $set = m('common')->getSysset();
                if (!empty($pay_result['verifycode'])) {
                    if($pset['sms']['type'] == 1){
                        if($pay_result['verifycode']['SubmitResult']['code'] == 2 || $allset['verify']['sendcode'] == 0 || empty($order['isverify'])){
                            return show_json(1, $pay_result);
                        }
                        else{
                            return show_json(0, $pay_result['verifycode']['SubmitResult']['msg']);
                        }
                    }
                    else{
                        if(isset($pay_result['verifycode']['result']['success']) || $allset['verify']['sendcode'] == 0 || empty($order['isverify'])){
                            return show_json(1, $pay_result);
                        }
                        else{
                            return show_json(0, $pay_result['verifycode']['msg']);
                        }
                    }
                } else {
                    return show_json(1, $pay_result);
                }
            }
            return show_json(0, '支付出错,请重试!');
        }
    }
}