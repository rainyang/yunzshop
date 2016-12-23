<?php
global $_W, $_GPC;
$openid = m('user')->getOpenid();
$cardid = intval($_GPC['cardid']);
$ordercount = pdo_fetchcolumn("SELECT count(1) FROM " . tablename('sz_yi_order') . " WHERE uniacid=:uniacid AND cardid=:cardid AND openid=:openid", array(
		':uniacid'	=> $_W['uniacid'],
		':openid'	=> $openid,
		':cardid'	=> $cardid
	));

if ($_W['isajax']) {
	$cardid = intval($_GPC['cardid']);
	$status = trim($_GPC['status']);
	if ($status != '') {
		$condition = '  AND status=' . intval($status);
	} else {
		$condition = '  AND status>=0';
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$list = array();
	$sql = "SELECT ordersn,cardprice,status,createtime FROM " . tablename('sz_yi_order') . " WHERE 1 {$condition} AND uniacid=:uniacid AND openid=:openid AND cardid=:cardid ORDER BY createtime DESC,status DESC  ";
	$sql .= " LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
	$list = pdo_fetchall($sql, 
		array(
		':uniacid' => $_W['uniacid'], 
		':openid' => $openid, 
		':cardid' => $cardid
		)
	);
	if (!empty($list)) {
		foreach ($list as &$row) {
			$row['createtime'] = date('Y-m-d H:i', $row['createtime']);
			if ($row['status'] == 0) {
				$row['statustype'] = '待付款';
			} else if ($row['status'] == 1) {
				$row['statustype'] = '已付款';
			} else if ($row['status'] == 3) {
				$row['statustype'] = '已完成';
			}
		}
		unset($row);
	}
	
    return show_json(1, array('list' => $list, 'ordercount' => $ordercount, 'pagesize' => $psize));
}


include $this->template('order');
