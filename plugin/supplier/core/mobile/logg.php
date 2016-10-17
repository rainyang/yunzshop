<?php

if (!defined('IN_IA')) {
	die('Access Denied');
}
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$openid = m('user')->getOpenid();
$member = m('member')->getMember($openid);
$supplieruser = $this->model->getSupplierUidAndUsername($openid);
$uid = $supplieruser['uid'];
$uniacid = $_W['uniacid'];
if ($_W['isajax']) {
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$condition = " and `uid`=:uid and uniacid=:uniacid";
	$params = array(':uid' => $uid, ':uniacid' => $uniacid);
	$status = trim($_GPC['status']);
	if ($status != '') {
		$condition .= ' and status=' . intval($status);
	}
    if (!empty($_GPC['id'])) {
        $condition .=' AND id<:id';
        $params[':id'] = intval($_GPC['id']);
    }
	$list = pdo_fetchall("select * from " . tablename('sz_yi_supplier_apply') . " where 1 {$condition} order by id desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
	$total = pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_supplier_apply') . " where 1 {$condition}", $params);
    $count = pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_supplier_apply') . " where 1 and `uid`=:uid and uniacid=:uniacid", array(':uniacid' => $_W['uniacid'],':uid' => $uid));
    $pageno = ceil($count/$psize);
	foreach ($list as &$row) {
		$row['apply_money'] = number_format($row['apply_money'],2);
		if ($row['status'] == 0) {
			$row['statusstr'] = '待审核';
			$row['dealtime'] = date('Y-m-d H:i', $row['apply_time']);
		} else {
			if ($row['status'] == 1) {
				$row['statusstr'] = '已打款';
				$row['dealtime'] = date('Y-m-d H:i', $row['finish_time']);
			}
		}
	}
	unset($row);
	return show_json(1, array('total' => $total, 'list' => $list, 'pagesize' => $psize, 'pageno' => $pageno));
}
if ($operation == 'display') {
	include $this->template('logg');
}