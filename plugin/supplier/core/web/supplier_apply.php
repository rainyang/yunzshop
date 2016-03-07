<?php
global $_W, $_GPC;
$operation   = empty($_GPC['op']) ? 'display' : $_GPC['op'];
if ($operation == 'display') {
	$where = '';
	if(!empty($_GPC['uid'])){
		$where .= ' and p.uid=' . $_GPC['uid'];
	}
	if(!empty($_GPC['applysn'])){
		$where .= ' and a.applysn=' . $_GPC['applysn'];
	} 
	$list = pdo_fetchall('select a.*,p.* from ' . tablename('sz_yi_supplier_apply') . ' a left join ' . tablename('sz_yi_perm_user') . ' p on p.uid=a.uid where a.status=0 and p.uniacid=' . $_W['uniacid'] . $where);
    $total = count($list);
} else if ($operation == 'detail') {
	$id = $_GPC['id'];
	if(!empty($id)){
		$data = array(
		'status' => 1,
		'finish_time' => time()
		);
		pdo_update('sz_yi_supplier_apply', $data, array(
				'id' => $id
			));
		message('通过成功!', $this->createPluginWebUrl('supplier/supplier_apply'), 'success');
	}
}
load()->func('tpl');
include $this->template('supplier_apply');