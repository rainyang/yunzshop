<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
function sortByTime($a, $b)
{
    if ($a['ts'] == $b['ts']) {
        return 0;
    } else {
        return $a['ts'] > $b['ts'] ? 1 : -1;
    }
}

$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$openid    = m('user')->getOpenid();
$uniacid   = $_W['uniacid'];
$orderid   = intval($_GPC['id']);
if ($_W['isajax']) {
	if ($operation == 'display') {
		$order = pdo_fetch('select * from ' . tablename('sz_yi_order') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1', array(':id' => $orderid, ':uniacid' => $uniacid, ':openid' => $openid));
		if (empty($order)) {
			return show_json(0);
		}
		$pindiana = p('indiana');
		$indiana = array();
		if($pindiana && $_GPC['indiana']){
			$indiana = $pindiana->getorder($order['period_num']);
		}
		$goods = pdo_fetchall("select og.goodsid,og.price,g.title,g.thumb,og.total,g.credit,og.optionid,og.optionname as optiontitle,g.isverify,g.storeids  from " . tablename('sz_yi_order_goods') . " og " . " left join " . tablename('sz_yi_goods') . " g on g.id=og.goodsid " . " where og.orderid=:orderid and og.uniacid=:uniacid ", array(':uniacid' => $uniacid, ':orderid' => $orderid));
		$goods = set_medias($goods, 'thumb');
		$order['goodstotal'] = count($goods);
		$set = set_medias(m('common')->getSysset('shop'), 'logo');
		return show_json(1, array('order' => $order, 'goods' => $goods, 'set' => $set, 'indiana' => $indiana));
	} else if ($operation == 'step') {
		$express = trim($_GPC['express']);
		$expresssn = trim($_GPC['expresssn']);
		$content = getExpress($express, $expresssn);
		if (!$content) {
			$content = getExpress($express, $expresssn);
			if (!$content) {
				return show_json(1, array('list' => array()));
			}
		}
		foreach ($content as $data) {
			$list[] = array('time' => $data->time, 'step' => $data->context, 'ts' => $data->time);
		}
		return show_json(1, array('list' => $list));
	}
}
include $this->template('order/express');
