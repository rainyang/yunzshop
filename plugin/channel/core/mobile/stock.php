<?php

if (!defined('IN_IA')) {
	die('Access Denied');
}
global $_W, $_GPC;
$operation 	= !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$openid 	= m('user')->getOpenid();
$uniacid 	= $_W['uniacid'];
if ($_W['isajax']) {
	$pindex 	= max(1, intval($_GPC['page']));
	$psize 		= 20;
	$condition 	= " and `openid`='{$openid}' and uniacid={$_W['uniacid']}";
	$list 		= pdo_fetchall("SELECT * FROM " . tablename('sz_yi_channel_stock') . " where 1 {$condition} order by id desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
	$total 		= pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('sz_yi_channel_apply') . " where 1 {$condition}");
	if (empty($total)) {
		$total = 0;
	}
	foreach ($list as &$rowp) {
		$sql = 'SELECT title,thumb FROM ' . tablename('sz_yi_goods') . " where id=:goodsid";
		$rowp['goods'] 		= set_medias(pdo_fetch($sql, array(':goodsid' => $rowp['goodsid'])), 'thumb');
	}
	unset($row);
	show_json(1, array('total' => $total, 'list' => $list, 'pagesize' => $psize));
}
include $this->template('stock');