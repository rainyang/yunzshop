<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$openid    = m('user')->getOpenid();
$uniacid   = $_W['uniacid'];
$orderid   = intval($_GPC['id']);
if ($_W['isajax']) {
	$order = pdo_fetch('select * from ' . tablename('sz_yi_order') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1', array(':id' => $orderid, ':uniacid' => $uniacid, ':openid' => $openid));
	if (empty($order)) {
		show_json(0);
	}
	$order['virtual_str'] = str_replace("\n", "<br/>", $order['virtual_str']);
	$goods = pdo_fetchall("select og.goodsid,og.price,g.title,g.thumb,og.total,g.credit,og.optionid,og.optionname as optiontitle,g.isverify,g.storeids  from " . tablename('sz_yi_order_goods') . " og " . " left join " . tablename('sz_yi_goods') . " g on g.id=og.goodsid " . " where og.orderid=:orderid and og.uniacid=:uniacid ", array(':uniacid' => $uniacid, ':orderid' => $orderid));
	$goods = set_medias($goods, 'thumb');
	$order['goodstotal'] = count($goods);
	$order['finishtimevalue'] = $order['finishtime'];
	$order['finishtime'] = date('Y-m-d H:i:s', $order['finishtime']);
	$address = false;
	$carrier = false;
	$stores = array();
	if ($order['isverify'] == 1) {
		$storeids = array();
		foreach ($goods as $g) {
			if (!empty($g['storeids'])) {
				$storeids = array_merge(explode(',', $g['storeids']), $storeids);
			}
		}
		if (empty($storeids)) {
			$stores = pdo_fetchall('select * from ' . tablename('sz_yi_store') . ' where  uniacid=:uniacid and status=1', array(':uniacid' => $_W['uniacid']));
		} else {
			$stores = pdo_fetchall('select * from ' . tablename('sz_yi_store') . ' where id in (' . implode(',', $storeids) . ') and uniacid=:uniacid and status=1', array(':uniacid' => $_W['uniacid']));
		}
	} else {
		if ($order['dispatchtype'] == 0) {
			$address = iunserializer($order['address']);
			if (!is_array($address)) {
				$address = pdo_fetch('select realname,mobile,address from ' . tablename('sz_yi_member_address') . ' where id=:id limit 1', array(':id' => $order['addressid']));
			}
		}
	}
	if ($order['dispatchtype'] == 1 || $order['isverify'] == 1 || !empty($order['virtual'])) {
		$carrier = unserialize($order['carrier']);
	}
	$set = set_medias(m('common')->getSysset('shop'), 'logo');
	$canrefund = false;
	if ($order['status'] == 1) {
		$canrefund = true;
	} else if ($order['status'] == 3) {
		if ($order['isverify'] != 1 && empty($order['virtual'])) {
			$tradeset = m('common')->getSysset('trade');
			$refunddays = intval($tradeset['refunddays']);
			if ($refunddays > 0) {
				$days = intval((time() - $order['finishtimevalue']) / 3600 / 24);
				if ($days <= $refunddays) {
					$canrefund = true;
				}
			}
		}
	}
	$order['canrefund'] = $canrefund;
	show_json(1, array('order' => $order, 'goods' => $goods, 'address' => $address, 'carrier' => $carrier, 'stores' => $stores, 'isverify' => $isverify, 'set' => $set));
}
include $this->template('order/detail');
