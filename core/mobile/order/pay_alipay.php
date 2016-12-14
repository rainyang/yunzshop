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
$uniacid = $_W['uniacid'];
$orderid = intval($_GPC['orderid']);
$logid   = intval($_GPC['logid']);
$shopset = m('common')->getSysset('shop');
if ($_W['isajax']) {
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
		$alipay = array('success' => false);
		$params = array();
		$params['tid'] = $order['ordersn_general'];
		$params['user'] = $openid;
		$params['fee'] = $order_price;
		$params['title'] = $param_title;
		load()->func('communication');
		load()->model('payment');
		$setting = uni_setting($_W['uniacid'], array('payment'));
		if (is_array($setting['payment'])) {
			$options = $setting['payment']['alipay'];
			$alipay = m('common')->alipay_build($params, $options, 0, $openid);
			if (!empty($alipay['url'])) {
				$alipay['success'] = true;
			}
		}
	} elseif (!empty($logid)) {
		$log = pdo_fetch('SELECT * FROM ' . tablename('sz_yi_member_log') . ' WHERE `id`=:id and `uniacid`=:uniacid limit 1', array(':uniacid' => $uniacid, ':id' => $logid));
		if (empty($log)) {
			return show_json(0, '充值出错!');
		}
		if (!empty($log['status'])) {
			return show_json(0, '已经充值成功,无需重复支付!');
		}
		$alipay = array('success' => false);
		$params = array();
		$params['tid'] = $log['logno'];
		$params['user'] = $log['openid'];
		$params['fee'] = $log['money'];
		$params['title'] = $log['title'];
		load()->func('communication');
		load()->model('payment');
		$setting = uni_setting($_W['uniacid'], array('payment'));
		if (is_array($setting['payment'])) {
			$options = $setting['payment']['alipay'];
			$alipay = m('common')->alipay_build($params, $options, 1, $openid);
			if (!empty($alipay['url'])) {
				$alipay['success'] = true;
			}
		}
	}
	return show_json(1, array('alipay' => $alipay));
}
include $this->template('order/pay_alipay');
