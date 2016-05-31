<?php
global $_W, $_GPC;
$operation   = empty($_GPC['op']) ? 'display' : $_GPC['op'];
if ($operation == 'display') {
	$where = '';
	if(!empty($_GPC['uid'])){
		$where .= ' and uid=' . $_GPC['uid'];
	}
	if(!empty($_GPC['applysn'])){
		$where .= ' and applysn=' . $_GPC['applysn'];
	}
    $list = pdo_fetchall("select * from " . tablename('sz_yi_supplier_apply') . " where uniacid={$_W['uniacid']} and status=1 {$where}");
    $total = count($list);
}
load()->func('tpl');
include $this->template('supplier_finish');