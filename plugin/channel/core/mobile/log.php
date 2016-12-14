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
	//$params 	= array(':openid' => $openid, ':uniacid' => $uniacid);
	$status 	= trim($_GPC['status']);
	if ($status != '') {
		$condition .= ' and status=' . intval($status);
	}
	$list 		= pdo_fetchall("SELECT * FROM " . tablename('sz_yi_channel_apply') . " where 1 {$condition} order by id desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
	$total 		= pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('sz_yi_channel_apply') . " where 1 {$condition}");
	if (empty($total)) {
		$total = 0;
	}
	foreach ($list as &$row) {
		$row['apply_money'] = number_format($row['apply_money'],2);
		if ($row['status'] == 1) {
			$row['statusstr'] = '待审核';
			$row['dealtime'] = date('Y-m-d H:i', $row['apply_time']);
		} else {
			if ($row['status'] == 3) {
				$row['statusstr'] = '已打款';
				$row['dealtime'] = date('Y-m-d H:i', $row['finish_time']);
			}
		}
	}
	unset($row);
	return show_json(1, array('total' => $total, 'list' => $list, 'pagesize' => $psize));
}
if ($operation == 'display') {
	include $this->template('log');
}