<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$openid    = m('user')->getOpenid();
if (empty($openid)) {
    $openid = $_GPC['openid'];
}
$member  = m('member')->getMember($openid);
$commission=p('commission')->getSet();
$uniacid = $_W['uniacid'];
$orderid = intval($_GPC['orderid']);
if ($operation == 'display' && $_W['isajax']) {
    if (empty($orderid)) {
        return show_json(0, '参数错误!');
    }
    $order = pdo_fetch(
        'select * from ' . tablename('sz_yi_order') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1',
        array(
            ':id' => $orderid,
            ':uniacid' => $uniacid,
            ':openid'  => $openid
        )
    );
    $store = pdo_fetch(
        'select * from ' . tablename('sz_yi_cashier_order') . ' o inner join ' . tablename('sz_yi_cashier_store') . ' s on o.cashier_store_id = s.id where o.order_id=:orderid and o.uniacid=:uniacid',
        array(
            ':uniacid' => $_W['uniacid'],
            ':orderid' => $orderid
        )
    );

    
    $couponurl = '';
    if (p('coupon') && $store['coupon_id'] > 0) {
        $couponurl = $this->createPluginMobileUrl('coupon/detail', array(
            'id' => $store['coupon_id']
        ));
    }else{
         $couponUrl = $this->createMobileUrl('member');
    }
    if (empty($order)) {
        return show_json(0, '订单未找到!');
    }
    if ($order['status'] == -1) {
        return show_json(-1, '订单已关闭, 无法付款!');
    } else if ($order['status'] >= 1) {
        return show_json(-1, '订单已付款, 无需重复支付!');
    }
    if(empty($order['ordersn_general'])){
        pdo_update('sz_yi_order', array(
            'ordersn_general' => $order['ordersn']
        ), array(
            'id' => $order['id']
        ));
        $order['ordersn_general'] = $order['ordersn'];
    }
    $log = pdo_fetch(
        'SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniacid`=:uniacid AND `module`=:module AND `tid`=:tid limit 1',
        array(
            ':uniacid' => $uniacid,
            ':module' => 'sz_yi',
            ':tid' => $order['ordersn_general']
        )
    );
    if (!empty($log) && $log['status'] != '0') {
        return show_json(-1, '订单已支付, 无需重复支付!');
    }
    if (!empty($log) && $log['status'] == '0') {
        pdo_delete('core_paylog', array(
            'plid' => $log['plid']
        ));
        $log = null;
    }
    $plid = $log['plid'];
    if (empty($log)) {
        $log = array(
            'uniacid' => $uniacid,
            'openid'  => $member['openid'],
            'module'  => "sz_yi",
            'tid'     => $order['ordersn_general'],
            'fee'     => $order['price'],
            'status'  => 0
        );
        pdo_insert('core_paylog', $log);
        $plid = pdo_insertid();
    }
    $set    = m('common')->getSysset(array('shop', 'pay'));
    $credit = array(
        'success' => false
    );
    $currentcredit = 0;
    if (isset($set['pay']) && $set['pay']['credit'] == 1) {
        if ($order['deductcredit2'] <= 0) {
            $credit = array(
                'success' => true,
                'current' => m('member')->getCredit($openid, 'credit2')
            );
        }
    }
    load()->model('payment');
    $setting = uni_setting($_W['uniacid'], array(
        'payment'
    ));
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

    $alipay = array(
        'success' => false
    );
    if (isset($set['pay']) && $set['pay']['alipay'] == 1) {
        if (is_array($setting['payment']['alipay']) && $setting['payment']['alipay']['switch']) {
            $alipay['success'] = true;
        }
    }

    $pluginy = p('yunpay');        
    $yunpay = array(
            'success' => false
        );
    if ($pluginy) {
        $yunpayinfo = $pluginy->getYunpay();
        if (isset($yunpayinfo) && $yunpayinfo['switch']) {
            $yunpay['success'] = true;
        }
    }

    $unionpay = array(
        'success' => false
    );
    if (isset($set['pay']) && $set['pay']['unionpay'] == 1) {
        if (is_array($setting['payment']['unionpay']) && $setting['payment']['unionpay']['switch']) {
            $unionpay['success'] = true;
        }
    }
    $returnurl = urlencode($this->createPluginMobileUrl('cashier/order_pay', array(
        'orderid' => $orderid
    )));
    return show_json(1, array(
        'order'         => $order,
        'set'           => $set,
        'credit'        => $credit,
        'wechat'        => $wechat,
        'alipay'        => $alipay,
        'unionpay'      => $unionpay,
        'yunpay'        => $yunpay,
        'cash'          => $cash,
        'isweixin'      => is_weixin(),
        'currentcredit' => $currentcredit,
        'returnurl'     => $returnurl,
        'couponurl'     => $couponurl
    ));
} else if ($operation == 'pay' && $_W['ispost']) {
    $set = m('common')->getSysset(array('shop', 'pay'));
    $order = pdo_fetch(
        "select * from " . tablename('sz_yi_order') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1',
        array(
            ':id'      => $orderid,
            ':uniacid' => $uniacid,
            ':openid'  => $openid
        )
    );
    if (empty($order)) {
        return show_json(0, '订单未找到!');
    }
    //支付方式应该支持商城所有支付接口  yitian_add::2016-12-27::qq::751818588
    $type = $_GPC['type'];
    if (!in_array($type, array('weixin', 'alipay', 'unionpay', 'yunpay'))) {
        return show_json(0, '未找到支付方式');
    }

    if($member['credit2'] < $order['deductcredit2'] && $order['deductcredit2'] > 0){
        return show_json(0, '余额不足，请充值后在试！');
    }

    $log = pdo_fetch(
        'SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniacid`=:uniacid AND `module`=:module AND `tid`=:tid limit 1',
        array(
            ':uniacid' => $uniacid,
            ':module'  => 'sz_yi',
            ':tid'     => $order['ordersn_general']
        )
    );
    if (empty($log)) {
        return show_json(0, '支付出错,请重试!');
    }

    $plid        = $log['plid'];
    $param_title = $set['shop']['name'] . "订单: " . $order['ordersn'];
    if(is_array($orderid)){
        $orderids = implode(',', $orderid);
        $where_update = "id in ({$orderids})";
    }else{
        $where_update = "id={$orderid}";
    }
    if ($type == 'weixin') {
        if (!empty($set['pay']['weixin']) || !empty($set['pay']['weixin_jie'])) {

        }else{
            return show_json(0, '未开启微信支付!');
        }

        $wechat        = array(
            'success' => false
        );
        $params        = array();
        $params['tid'] = $log['tid'];
        if (!empty($order['ordersn2'])) {
            $var = sprintf("%02d", $order['ordersn2']);
            $params['tid'] .= "GJ" . $var;
        }
        $params['user']  = $openid;
        $params['fee']   = $order['price'];
        $params['title'] = $param_title;
        load()->model('payment');   //todo,貌似没有使用
        $setting = uni_setting($_W['uniacid'], array(
            'payment'
        ));
        //微信下
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            if (is_array($setting['payment'])) {
                $options           = $setting['payment']['wechat'];
                if (is_weixin()) {
                    if(empty($set['pay']['weixin_jie'])){
                        $options['appid'] = $_W['account']['key'];
                        $options['secret'] = $_W['account']['secret'];
                        $wechat            = m('common')->wechat_build($params, $options, 0);
                        //$wechat['success'] = false;
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
            pdo_query('update ' . tablename('sz_yi_order') . ' set paytype=21 where '.$where_update.' and uniacid=:uniacid ', array(
                ':uniacid' => $uniacid
            ));
            show_json(1, array(
                'wechat' => $wechat
            ));
        }
        elseif(is_app_api()){//新版app原生支付
            $options           = $setting['payment']['wechat'];
            $pay = $setting['payment'];
            $options['mchid'] = $pay['wx_native']['wx_mcid'];
            $options['appid'] = $pay['wx_native']['wx_appid'];
            $options['secret'] = $pay['wx_native']['wx_secret'];
            $options['signkey'] = $pay['wx_native']['signkey'];

            $params['trade_type'] = 'APP';
            $wechat            = m('common')->wechat_build($params, $options, 0);
            //$wechat['success'] = false;
            if (!is_error($wechat)) {
                $wechat['success'] = true;
            } else {
                return show_json(0, $wechat['message']);
            }
            if (!$wechat['success']) {
                return show_json(0, '微信支付参数错误!');
            }
        }
        else{   //PC端微信扫码pay
            if (is_array($setting['payment'])) {
                $params['trade_type']  = 'NATIVE';
                $options           = $setting['payment']['wechat'];
                $options['appid']  = $_W['account']['key'];
                $options['secret'] = $_W['account']['secret'];
                $wechat            = m('common')->wechat_build($params, $options, 0);
                //print_r($wechat);exit;
                //$wechat['success'] = false;

                if (!is_error($wechat)) {
                    $wechat['success'] = true;
                    $wechat['code_url'] = m('qrcode')->createWechatQrcode($wechat['code_url']);
                    //$wechat['code_url'] = $wechat['code_url'];
                } else {
                    return show_json(0, $wechat['message']);
                }
            }
        }
        pdo_query('update ' . tablename('sz_yi_order') . ' set paytype=21 where '.$where_update.' and uniacid=:uniacid ', array(
            ':uniacid' => $uniacid
        ));
        return show_json(1, array(
            'wechat' => $wechat
        ));
    } else if ($type == 'alipay') {
        pdo_update('sz_yi_order', array(
            'paytype' => 22
        ), array(
            'id' => $order['id']
        ));
        if($commission['become_child']==2){
             p('commission')->checkOrderPay($orderid);
        }

        return show_json(1);
    }else if ($type == 'yunpay') {
        pdo_update('sz_yi_order', array(
            'paytype' => 24
        ), array(
            'id' => $order['id']
        ));
        if($commission['become_child']==2){
             p('commission')->checkOrderPay($orderid);
        }

        return show_json(1);
    }
} else if ($operation == 'complete' && $_W['ispost']) {
    $order = pdo_fetch(
        'select * from ' . tablename('sz_yi_order') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1',
        array(
            ':id' => $orderid,
            ':uniacid' => $uniacid,
            ':openid' => $openid
        )
    );
    $store = pdo_fetch(
        'select * from ' . tablename('sz_yi_cashier_order') . ' o inner join ' . tablename('sz_yi_cashier_store') . ' s on o.cashier_store_id = s.id where o.order_id=:orderid and o.uniacid=:uniacid',
        array(
            ':uniacid' => $_W['uniacid'],
            ':orderid' => $orderid
        )
    );
    if (p('coupon') && $store['coupon_id'] > 0) {
        $couponUrl = $this->createPluginMobileUrl('coupon/detail', array(
            'id' => $store['coupon_id']
        ));
    }else{
         $couponUrl = $this->createMobileUrl('member');
    }
    if (empty($order)) {
        return show_json(0, '订单未找到!');
    }
    $type = $_GPC['type'];
    if (!in_array($type, array('weixin', 'alipay', 'credit'))) {
        return show_json(0, '未找到支付方式');
    }
    if($member['credit2'] < $order['deductcredit2'] && $order['deductcredit2'] > 0){
        return show_json(0, '余额不足，请充值后在试！');
    }
    $log = pdo_fetch(
        'SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniacid`=:uniacid AND `module`=:module AND `tid`=:tid limit 1',
        array(
            ':uniacid' => $uniacid,
            ':module' => 'sz_yi',
            ':tid' => $order['pay_ordersn']
        )
    );
    if (empty($log)) {
        return show_json(0, '支付出错,请重试!');
    }
    $plid        = $log['plid'];
    $ps          = array();
    $ps['tid']   = $log['tid'];
    $ps['user']  = $openid;
    $ps['fee']   = $log['fee'];
    $ps['title'] = $log['title'];
    if ($type == 'credit') {
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
        pdo_update('sz_yi_order', array(
            'paytype' => 1
        ), array(
            'id' => $order['id']
        ));
        $ret            = array();
        $ret['result']  = 'success';
        $ret['type']    = $log['type'];
        $ret['from']    = 'return';
        $ret['tid']     = $log['tid'];
        $ret['user']    = $log['openid'];
        $ret['fee']     = $log['fee'];
        $ret['weid']    = $log['weid'];
        $ret['uniacid'] = $log['uniacid'];
        
        
        
        $pay_result     = $this->model->payResult($ret);

        $pay_result['couponurl'] = $couponUrl;
        $pay_result['order'] = $order;
        if($commission['become_child']==2){
             p('commission')->checkOrderPay($orderid);
        }
       
        $this->model->redpack($openid,$orderid);
        //$this->model->setCredits($orderid);
        $this->model->setCredits2($orderid);
        return show_json(1, $pay_result);

    } else if ($type == 'weixin') {
        $ordersn =  $order['pay_ordersn'] ? $order['pay_ordersn'] : $order['ordersn_general'];
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
            $ret['deduct']  = intval($_GPC['deduct']) == 1;
            $pay_result     = $this->model->payResult($ret);

            if($commission['become_child']==2){
                 p('commission')->checkOrderPay($orderid);
            }

            $this->model->redpack($openid,$orderid);
            $this->model->setCredits($orderid);
            $this->model->setCredits2($orderid);
            $pay_result['couponurl'] = $couponUrl;
            $pay_result['order'] = $order;
            return show_json(1,$pay_result);



        }
        return show_json(0, '支付出错,请重试!');
    }
} else if ($operation == 'return') {
    $tid = $_GPC['out_trade_no'];
    if (!m('finance')->isAlipayNotify($_GET)) {
        die('支付出现错误，请重试!');
    }
    $log = pdo_fetch(
        'SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniacid`=:uniacid AND `module`=:module AND `tid`=:tid limit 1',
        array(
            ':uniacid' => $uniacid,
            ':module' => 'sz_yi',
            ':tid' => $tid
        )
    );
    if (empty($log)) {
        die('支付出现错误，请重试!');
    }
    if ($log['status'] != 1) {
        $record           = array();
        $record['status'] = '1';
        $record['type']   = 'alipay';
        pdo_update('core_paylog', $record, array(
            'plid' => $log['plid']
        ));
        $ret            = array();
        $ret['result']  = 'success';
        $ret['type']    = 'alipay';
        $ret['from']    = 'return';
        $ret['tid']     = $log['tid'];
        $ret['user']    = $log['openid'];
        $ret['fee']     = $log['fee'];
        $ret['weid']    = $log['weid'];
        $ret['uniacid'] = $log['uniacid'];
        $this->model->payResult($ret);
    }
    $orderid = pdo_fetchcolumn(
        'select id from ' . tablename('sz_yi_order') . ' where ordersn_general=:ordersn and uniacid=:uniacid',
        array(
            ':ordersn' => $log['tid'],
            ':uniacid' => $_W['uniacid']
        )
    );
    $store = pdo_fetch(
        'select * from ' . tablename('sz_yi_cashier_order') . ' o inner join ' . tablename('sz_yi_cashier_store') . ' s on o.cashier_store_id = s.id where o.order_id=:orderid and o.uniacid=:uniacid',
        array(
            ':uniacid' => $_W['uniacid'],
            ':orderid' => $orderid
        )
    );
    // TODO: 跳转到营销页面
    if (p('coupon') && $store['coupon_id'] > 0) {
        $url = $this->createPluginMobileUrl('coupon/detail', array(
            'id' => $store['coupon_id']
        ));
    }else{
         $url = $this->createMobileUrl('member');
    }
    die("<script>top.window.location.href='{$url}'</script>");
} else if ($operation == 'returnyunpay') {
    $tids = $_REQUEST['i2'];
    $strs = explode(':', $tids);
    $tid  = $strs [0];
    $pluginy = p('yunpay');
    if (!$pluginy->isYunpayNotify($_GET)) {
        die('支付出现错误，请重试!');
    }
    $log = pdo_fetch(
        'SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniacid`=:uniacid AND `module`=:module AND `tid`=:tid limit 1',
        array(
            ':uniacid' => $uniacid,
            ':module' => 'sz_yi',
            ':tid' => $tid
        )
    );
    if (empty($log)) {
        die('支付出现错误，请重试!');
    }
    if ($log['status'] != 1) {
        $record           = array();
        $record['status'] = '1';
        $record['type']   = 'yunpay';
        pdo_update('core_paylog', $record, array(
            'plid' => $log['plid']
        ));
        $ret            = array();
        $ret['result']  = 'success';
        $ret['type']    = 'yunpay';
        $ret['from']    = 'return';
        $ret['tid']     = $log['tid'];
        $ret['user']    = $log['openid'];
        $ret['fee']     = $log['fee'];
        $ret['weid']    = $log['weid'];
        $ret['uniacid'] = $log['uniacid'];
        $this->model->payResult($ret);
    }
    $orderid = pdo_fetchcolumn(
        'select id from ' . tablename('sz_yi_order') . ' where ordersn_general=:ordersn and uniacid=:uniacid',
        array(
            ':ordersn' => $log['tid'],
            ':uniacid' => $_W['uniacid']
        )
    );
    $store = pdo_fetch(
        'select * from ' . tablename('sz_yi_cashier_order') . ' o inner join ' . tablename('sz_yi_cashier_store') . ' s on o.cashier_store_id = s.id where o.order_id=:orderid and o.uniacid=:uniacid',
        array(
            ':uniacid' => $_W['uniacid'],
            ':orderid' => $orderid
        )
    );
    // TODO: 跳转到营销页面
    if (p('coupon') && $store['coupon_id'] > 0) {
        $url = $this->createPluginMobileUrl('coupon/detail', array(
            'id' => $store['coupon_id']
        ));
    }else{
         $url = $this->createMobileUrl('member');
    }
    die("<script>top.window.location.href='{$url}'</script>");
}

if ($operation == 'display') {
    include $this->template('cashier/order_pay');
}