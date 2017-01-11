<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/1/11
 * Time: 上午10:31
 */
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;

$shopset   = m('common')->getSysset('shop');
$openid    = m('user')->getOpenid();
if (empty($openid)) {
    $openid = $_GPC['openid'];
}

$set   = m('common')->getSysset(array('shop', 'pay'));
$member  = m('member')->getMember($openid);
$uniacid = $_W['uniacid'];
$orderid = intval($_GPC['orderid']);

if(!empty($orderid)){
    $ordersn_general = pdo_fetchcolumn("select ordersn_general from " . tablename('sz_yi_order') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1', array(
        ':id' => $orderid,
        ':uniacid' => $uniacid,
        ':openid' => $openid
    ));
    $order_all = pdo_fetchall("select * from " . tablename('sz_yi_order') . ' where ordersn_general=:ordersn_general and uniacid=:uniacid and openid=:openid', array(
        ':ordersn_general' => $ordersn_general,
        ':uniacid' => $uniacid,
        ':openid' => $openid
    ));
    //合并订单号订单大于1个，执行合并付款
    if(count($order_all) > 1){
        $order = array();
        $order['ordersn'] = $ordersn_general;
        $orderid = array();
        foreach ($order_all as $key => $val) {
            $order['price']           += $val['price'];
            $order['deductcredit2']   += $val['deductcredit2'];
            $order['ordersn2']   	  += $val['ordersn2'];
            $orderid[]				   = $val['id'];
        }
        $order['status']	= $val['status'];
        $order['cash']		= $val['cash'];
        $order['openid']		= $val['openid'];
        $order['pay_ordersn']        = $val['pay_ordersn'];
    }else{
        $order = $order_all[0];
    }

}

if ($_W['isajax']) {
    if (empty($order)) {
        return show_json(0, '订单未找到!');
    }


    if($member['credit2'] < $order['deductcredit2'] && $order['deductcredit2'] > 0){
        return show_json(0, '余额不足，请充值后在试！');
    }
    $pay_ordersn = $order['pay_ordersn'] ? $order['pay_ordersn'] : $ordersn_general;
    $log = pdo_fetch('SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniacid`=:uniacid AND `module`=:module AND `tid`=:tid limit 1', array(
        ':uniacid' => $uniacid,
        ':module' => 'sz_yi',
        ':tid' => $pay_ordersn
    ));
    if (empty($log)) {
        return show_json(0, '支付出错,请重试!');
    }
    if(is_array($orderid)){
        $orderids = implode(',', $orderid);
        $where_orderid = "og.orderid in ({$orderids})";
    }else{
        $where_orderid = "og.orderid={$orderid}";
    }

    $order_goods = pdo_fetchall('select og.id,g.type,g.title, og.goodsid,og.optionid,g.total as stock,og.total as buycount,g.status,g.deleted,g.maxbuy,g.usermaxbuy,g.istime,g.timestart,g.timeend,g.buylevels,g.buygroups from  ' . tablename('sz_yi_order_goods') . ' og ' . ' left join ' . tablename('sz_yi_goods') . ' g on og.goodsid = g.id ' . ' where '.$where_orderid.' and og.uniacid=:uniacid ', array(
        ':uniacid' => $_W['uniacid']
    ));
    if (p('recharge')) {
        if ($order_goods['type'] == 11 || $order_goods['type'] == 12) {
            $order_goods_recharge = pdo_fetch('select go.title,g.type,o.carrier,o.price from ' . tablename('sz_yi_order') . 'o left join '. tablename('sz_yi_order_goods') .' og ' .' on o.id=og.orderid left join ' . tablename('sz_yi_goods') .' g on og.goodsid=g.id left join'. tablename('sz_yi_goods_option') .' go on og.optionid=go.id where o.id=:id and o.uniacid=:uniacid and o.openid=:openid', array(':id' => $orderid, ':uniacid' => $uniacid, ':openid' => $openid));
            $order['mobile'] = $_GPC['telephone'];
            $order['title'] = $order_goods_recharge['title'];
            preg_match('/\d+/',$order_goods_recharge['title'],$packcode);

            $unit = substr($order_goods_recharge['title'],-1);

            if (strtoupper($unit) == "G") {
                $packcode = $packcode[0]*1024;
            } else {
                $packcode = $packcode[0];
            }

            if(!empty($order_goods)){
                $carrier = unserialize($order_goods['carrier']);
            }
            $mobile_data_param = array();
            $mobile_data_param['apikey']    =   $rechargeset['rechargeapikey'];
            $mobile_data_param['username']    =   $rechargeset['rechargeusername'];
            $mobile_data_param['price']     =   $order_goods_recharge['price'];
            p('recharge')->mobile_blance_api($mobile_data_param);
        }


    }

    foreach ($order_goods as $data) {
        if (empty($data['status']) || !empty($data['deleted'])) {
            return show_json(-1, $data['title'] . '<br/> 已下架!');
        }
        if ($data['maxbuy'] > 0) {
            if ($data['buycount'] > $data['maxbuy']) {
                return show_json(-1, $data['title'] . '<br/> 一次限购 ' . $data['maxbuy'] . $unit . "!");
            }
        }
        if ($data['usermaxbuy'] > 0) {
            $order_goodscount = pdo_fetchcolumn('select ifnull(sum(og.total),0)  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join ' . tablename('sz_yi_order') . ' o on og.orderid=o.id ' . ' where og.goodsid=:goodsid and  o.status>=1 and o.openid=:openid  and og.uniacid=:uniacid ', array(
                ':goodsid' => $data['goodsid'],
                ':uniacid' => $uniacid,
                ':openid' => $openid
            ));
            if ($order_goodscount >= $data['usermaxbuy']) {
                return show_json(-1, $data['title'] . '<br/> 最多限购 ' . $data['usermaxbuy'] . $unit . "!");
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
        if (!empty($data['optionid'])) {
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
    $plid        = $log['plid'];
    $param_title = $set['shop']['name'] . "订单: " . $order['ordersn'];
    if(is_array($orderid)){
        $orderids = implode(',', $orderid);
        $where_update = "id in ({$orderids})";
    }else{
        $where_update = "id={$orderid}";
    }

    //后台订单列表增加paytype
    pdo_query('update ' . tablename('sz_yi_order') . ' set paytype=30 where '.$where_update.' and uniacid=:uniacid ', array(
            ':uniacid' => $uniacid
    ));

    $params = pay_gaohuitong($openid, $orderid, $shopset);

    load()->func('communication');
    load()->model('payment');
    $setting = uni_setting($_W['uniacid'], array('payment'));
    if (is_array($setting['payment'])) {
        $options = pdo_fetch("select * from " . tablename('sz_yi_gaohuitong') . ' where uniacid=:uniacid limit 1', array(
            ':uniacid' => $_W['uniacid']
        ));

        $gaohuitong = $this->model->gaohuitong_build($params, $options, 0, $openid);
        if (!empty($gaohuitong['url'])) {
            $gaohuitong['success'] = true;
        }
    }

    return show_json(1, array('gaohuitong' => $gaohuitong));
}

function pay_gaohuitong($openid,$orderid = 0, $shopset)
{
    global $_W;

    $uniacid = $_W['uniacid'];

    if (!empty($orderid)) {
        $order = pdo_fetch("select * from " . tablename('sz_yi_order') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1', array(':id' => $orderid, ':uniacid' => $uniacid, ':openid' => $openid));
        if (empty($order)) {
            return show_json(0, '订单未找到!');
        }
        $order_price = pdo_fetchcolumn("select sum(price) from " . tablename('sz_yi_order') . ' where ordersn_general=:ordersn_general and uniacid=:uniacid and openid=:openid limit 1', array(':ordersn_general' => $order['ordersn_general'], ':uniacid' => $uniacid, ':openid' => $openid));
        $log = pdo_fetch('SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniacid`=:uniacid AND `module`=:module AND `tid`=:tid limit 1', array(':uniacid' => $uniacid, ':module' => 'sz_yi', ':tid' => $order['ordersn_general']));
        if (!empty($log) && $log['status'] != '0') {
            return show_json(0, '订单已支付, 无需重复支付!');
        }
        $param_title = $shopset['name'] . "订单: " . $order['ordersn_general'];
        $taohuitong = array('success' => false);
        $params = array();
        $params['tid'] = $order['ordersn_general'];
        $params['user'] = $openid;
        $params['fee'] = $order_price;
        $params['title'] = $param_title;
        return $params;
    }
}