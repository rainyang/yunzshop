<?php
global $_W, $_GPC;
$operation   = empty($_GPC['op']) ? 'display' : $_GPC['op'];
if ($operation == 'display') {
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$where = '';
	if(!empty($_GPC['uid'])){
		$where .= ' and uid=' . $_GPC['uid'];
	}
	if(!empty($_GPC['applysn'])){
		$where .= ' and applysn=' . $_GPC['applysn'];
	}
    $list = pdo_fetchall("select * from " . tablename('sz_yi_supplier_apply') . " where uniacid={$_W['uniacid']} and status=1 {$where}" . '  limit ' . ($pindex - 1) * $psize . ',' . $psize);
    $total = pdo_fetchcolumn("select count(id) from " . tablename('sz_yi_supplier_apply') . " where uniacid={$_W['uniacid']} and status=1 {$where}");
    $pager = pagination($total, $pindex, $psize);
}
load()->func('tpl');
include $this->template('supplier_finish');