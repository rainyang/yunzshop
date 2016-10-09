<?php
global $_W, $_GPC;
$openid   = m('user')->getOpenid();
$member = m('member')->getMember($openid);
$iscenter = intval($_GPC['iscenter']);
if (empty($iscenter)) {
	$suppliers = pdo_fetchall("select distinct supplier_uid from " . tablename('sz_yi_merchants') . " where member_id={$member['id']} and uniacid={$_W['uniacid']}");
	$total = count($suppliers);
	$uids = '';
	foreach ($suppliers as $key => $value) {
	    if ($key == 0) {
	        $uids .= $value['supplier_uid'];
	    } else {
	        $uids .= ','.$value['supplier_uid'];
	    }
	}
	if (empty($uids)) {
	    $uids = 0;
	}
}


$condition = '';
if ($_W['isajax']) {
	$iscenter = intval($_GPC['iscenter']);
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$list = array();
	
	if (empty($iscenter)) {
		$list = pdo_fetchall("select * from " . tablename('sz_yi_perm_user') . " where uid in ({$uids}) and status=1 and uniacid = " . $_W['uniacid'] . " {$condition}  ORDER BY id desc limit " . ($pindex - 1) * $psize . ',' . $psize);
		foreach ($list as &$row) {
	        $supplier_commissions = pdo_fetchcolumn("select commissions from " . tablename('sz_yi_merchants') . " where uniacid={$_W['uniacid']} and supplier_uid={$row['uid']} and member_id={$member['id']}");
	        $supplier_order_price_total = pdo_fetchcolumn("select sum(price) from " . tablename('sz_yi_order') . " where uniacid={$_W['uniacid']} and status=3 and deleted=0 and supplier_uid={$row['uid']}");
			$row['commission_total'] = $supplier_commissions*$supplier_order_price_total/100;
			$row['ordercount'] = pdo_fetchcolumn("select count(*) from " . tablename('sz_yi_order') . " where uniacid={$_W['uniacid']} and deleted=0 and status=3 and supplier_uid={$row['uid']}");
	        $supplier_openid = pdo_fetchcolumn("select openid from " . tablename('sz_yi_perm_user') . " where uniacid={$_W['uniacid']} and uid={$row['uid']}");
	        $row['realname'] = pdo_fetchcolumn("select realname from " . tablename('sz_yi_member') . " where uniacid={$_W['uniacid']} and openid='{$supplier_openid}'");
		}
		unset($row);
	} else {
		$center_id = intval($_GPC['center_id']);
		if (empty($center_id)) {
			$list = $this->model->getChildCenters($openid);
			foreach ($list as &$val) {
				if (!empty($val['level_id'])) {
					$val['level'] = pdo_fetch("SELECT * FROM " . tablename('sz_yi_merchant_level') . " WHERE uniacid=:uniacid AND id=:id", array(':uniacid' => $_W['uniacid'], ':id' => $val['level_id']));
				}
			}
			unset($val);
		} else {
			$list = $this->model->getCenterMerchants($center_id);
			foreach ($list as &$val) {
				if (!empty($val['openid'])) {
					$member = pdo_fetch("SELECT realname,mobile FROM " . tablename('sz_yi_member') . " WHERE uniacid=:uniacid AND openid=:openid", array(':uniacid' => $_W['uniacid'], ':openid' => $val['openid']));
					$val['username'] = $member['realname'];
					$val['mobile'] = $member['mobile'];
					$val['level']['level_name'] = '招商员';
					$val['level']['commission'] = $val['commissions'];

				}
			}
			unset($val);
		}
	}
	show_json(1, array('list' => $list, 'pagesize' => $psize));
}
include $this->template('team');
