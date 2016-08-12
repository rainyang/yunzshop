<?php
global $_W, $_GPC;
$operation = empty($_GPC['op']) ? 'display' : $_GPC['op'];
if ($operation == 'display') {
	$pindex = max(1, intval($_GPC['page']));
	$params = array();
	$psize = 20;
	$condition = "";
	if (!empty($_GPC['realname'])) {
		$_GPC['realname'] = trim($_GPC['realname']);
		$condition .= ' and (realname like :realname or nickname like :realname or mobile like :realname)';
		$params[':realname'] = "%{$_GPC['realname']}%";
	}
	$ids = "";
	$member_ids = pdo_fetchall("select distinct member_id from " . tablename('sz_yi_merchants') . " where uniacid={$_W['uniacid']}");
	if (!empty($_GPC['mid'])) {
		$ids = intval($_GPC['mid']);
	} else {
		foreach ($member_ids as $key => $value) {
			if ($key == 0) {
				$ids .= $value['member_id'];
			} else {
				$ids .= ','.$value['member_id'];
			}
		}
	}
	if (empty($ids)) {
		$ids = 0;
	}
	$sql = "select id, avatar, nickname, realname, mobile from " . tablename('sz_yi_member') . " where uniacid={$_W['uniacid']} and id in ({$ids}) {$condition}";
	if (empty($_GPC['export'])) {
		$sql .= '  limit ' . ($pindex - 1) * $psize . ',' . $psize;
	}
	$list = pdo_fetchall($sql, $params);
	foreach ($list as &$value) {
		$suppliers = pdo_fetchall("select distinct supplier_uid from " . tablename('sz_yi_merchants') . " where member_id={$value['id']} and uniacid={$_W['uniacid']}");
		$value['commissions'] = pdo_fetchcolumn("select commissions from " . tablename('sz_yi_merchants') . " where uniacid={$_W['uniacid']} and member_id={$value['id']}");
		$value['commission_ok'] = pdo_fetchcolumn("select sum(money) from " . tablename('sz_yi_merchant_apply') . " where uniacid={$_W['uniacid']} and status=1 and member_id={$value['id']}");
		$value['suppliercount'] = count($suppliers);
		$value['ordercount'] = 0;
		$value['commission_total'] = 0;
		foreach ($suppliers as $val) {
			$value['ordercount'] += pdo_fetchcolumn("select count(*) from " . tablename('sz_yi_order') . " where uniacid={$_W['uniacid']} and status=3 and supplier_uid={$val['supplier_uid']}");
			$com_total = pdo_fetchcolumn("select sum(price) from " . tablename('sz_yi_order') . " where uniacid={$_W['uniacid']} and status=3 and supplier_uid={$val['supplier_uid']}");
			$com_total = number_format($value['commissions']*$com_total/100,2);
			$value['commission_total'] += $com_total;
		}
	}
	unset($value);
	if ($_GPC['export'] == '1') {
		m('excel')->export($list, array('title' => '招商员' . '数据-' . date('Y-m-d-H-i', time()), 'columns' => array(array('title' => 'ID', 'field' => 'id', 'width' => 12), array('title' => '粉丝', 'field' => 'nickname', 'width' => 12), array('title' => '姓名', 'field' => 'realname', 'width' => 12), array('title' => '手机号码', 'field' => 'mobile', 'width' => 12), array('title' => '供应商数', 'field' => 'suppliercount', 'width' => 12), array('title' => '订单数', 'field' => 'ordercount', 'width' => 12), array('title' => '佣金比例', 'field' => 'commissions', 'width' => 12), array('title' => '累积佣金', 'field' => 'commission_total', 'width' => 12), array('title' => '打款佣金', 'field' => 'commission_ok', 'width' => 12))));
	}
	$total = count($list);
	$pager = pagination($total, $pindex, $psize);
} else if ($operation == 'merchant_sp') {
	$member_id = intval($_GPC['member_id']);
	$supplier_uids = pdo_fetchall("select distinct supplier_uid from " . tablename('sz_yi_merchants') . " where uniacid={$_W['uniacid']} and member_id={$member_id}");
	$uids = "";
	foreach ($supplier_uids as $key => $value) {
		if ($key == 0) {
			$uids .= $value['supplier_uid'];
		} else {
			$uids .= ','.$value['supplier_uid'];
		}
	}
}
load()->func('tpl');
include $this->template('merchants');
