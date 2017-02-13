<?php
global $_W, $_GPC;
$openid   = m('user')->getOpenid();
$member = m('member')->getMember($openid);
$suppliers = pdo_fetchall("select distinct supplier_uid from " . tablename('sz_yi_merchants') . " where member_id={$member['id']} and uniacid={$_W['uniacid']}");
$total = pdo_fetchcolumn('SELECT count(distinct supplier_uid) FROM ' . tablename('sz_yi_merchants') . ' WHERE uniacid = :uniacid AND member_id = :member_id', array(
    ':uniacid'      => $_W['uniacid'],
    ':member_id'    => $member['id']
));
$uids = '';
if (!empty($suppliers)) {
    $uids = array();
    foreach ($suppliers AS $v) {
        $uids[] = $v['supplier_uid'];
    }
    $uids = implode(',', $uids);
}
if (empty($uids)) {
    $uids = 0;
}
if ($_W['isajax']) {
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$list = array();
	$list = pdo_fetchall("select * from " . tablename('sz_yi_perm_user') . " where uid in ({$uids}) and status=1 and uniacid = " . $_W['uniacid'] . " ORDER BY id desc limit " . ($pindex - 1) * $psize . ',' . $psize);

	foreach ($list as &$row) {
        $supplier_commissions = pdo_fetchcolumn("select commissions from " . tablename('sz_yi_merchants') . " where uniacid={$_W['uniacid']} and supplier_uid={$row['uid']} and member_id={$member['id']}");

        $supplier_order_price_total = pdo_fetchcolumn("select sum(price) from " . tablename('sz_yi_order') . " where uniacid={$_W['uniacid']} and status=3 and deleted=0 and supplier_uid={$row['uid']}");

		$row['commission_total'] = $supplier_commissions*$supplier_order_price_total/100;

		$row['ordercount'] = pdo_fetchcolumn("select count(*) from " . tablename('sz_yi_order') . " where uniacid={$_W['uniacid']} and deleted=0 and status=3 and supplier_uid={$row['uid']}");

        $supplier_openid = pdo_fetchcolumn("select openid from " . tablename('sz_yi_perm_user') . " where uniacid={$_W['uniacid']} and uid={$row['uid']}");

        $row['realname'] = pdo_fetchcolumn("select realname from " . tablename('sz_yi_member') . " where uniacid={$_W['uniacid']} and openid='{$supplier_openid}'");


	}
	unset($row);
return show_json(1, array('list' => $list, 'pagesize' => $psize));
}
include $this->template('team');
