<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$operation  = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$openid     = m('user')->getOpenid();
$mid        = m('member')->getMid();
$uniacid    = $_W['uniacid'];
$storeid = intval($_GPC['storeid']);
if ($_W['isajax']) {
	if ($operation == 'display') {
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$condition = " and `store_id`=:storeid and uniacid=:uniacid";
		$params = array(':storeid' => $storeid, ':uniacid' => $uniacid);
		$status = trim($_GPC['status']);

		if ($status != '') {
			$condition .= ' and status=' . intval($status);
		}

		$list = pdo_fetchall("select * from " . tablename('sz_yi_store_withdraw') . " where 1 {$condition} order by id desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
		$total = pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_store_withdraw') . " where 1 {$condition}", $params);
		foreach ($list as &$row) {
            if ($row['apply_time']) {
                $row['apply_time'] = date('Y-m-d H:i:s', $row['apply_time']);
            }
            if ($row['refuse_time']) {
                $row['refuse_time'] = date('Y-m-d H:i:s', $row['refuse_time']);
            }
			if ($row['status'] == 0) {
				$row['statusstr'] = '待打款';
				$row['dealtime'] = date('Y-m-d H:i', $row['checktime']);
			} else if ($row['status'] == 1) {
				$row['statusstr'] = '已打款';
				$row['dealtime'] = date('Y-m-d H:i', $row['paytime']);
			} else if ($row['status'] == 2) {
				$row['dealtime'] = date('Y-m-d H:i', $row['invalidtime']);
				$row['statusstr'] = '无效';
			}
		}
		unset($row);
		return show_json(1, array('total' => $total, 'list' => $list, 'pagesize' => $psize, 'commissioncount' => number_format($commissioncount, 2)));
	}
}
if ($operation == 'display') {
    include $this->template('log');
}

