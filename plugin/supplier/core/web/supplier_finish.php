<?php
global $_W, $_GPC;
$operation   = empty($_GPC['op']) ? 'display' : $_GPC['op'];
if ($operation == 'display') {
	$where = '';
	if(!empty($_GPC['uid'])){
		$where .= ' u.uid=' . $_GPC['uid'];
	}
	if(!empty($_GPC['applysn'])){
		$where .= ' and a.applysn=' . $_GPC['applysn'];
	}
    $list = pdo_fetchall('select a.id,p.realname,p.mobile,p.banknumber,p.accountname,p.accountbank,a.applysn,a.apply_money,a.apply_time,a.apply_time from ' . tablename('sz_yi_supplier_apply') . ' a left join ' . tablename('sz_yi_perm_user') . ' p on p.uid=a.uid where a.status=1 and p.uniacid=' . $_W['uniacid'] . $where);
    $total = count($list);
}
load()->func('tpl');
include $this->template('supplier_finish');