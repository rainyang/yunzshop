<?php
global $_W, $_GPC;

$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') {
    $list = pdo_fetchall("SELECT * FROM " . tablename('sz_yi_merchant_level') . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY commission asc");
} elseif ($operation == 'post') {
	$id = intval($_GPC['id']);
	$level = pdo_fetch("SELECT * FROM " . tablename('sz_yi_merchant_level') . " WHERE id = '$id'");
	if (checksubmit('submit')) {
		if (empty($_GPC['level_name'])) {
			message('抱歉，请输入等级名称！');
		}
		$data = array(
			'uniacid' => $_W['uniacid'], 
			'level_name' => $_GPC['level_name'], 
			'commission' => $_GPC['commission']
			);
		if (!empty($id)) {
			pdo_update('sz_yi_merchant_level', $data, array('id' => $id, 'uniacid' => $_W['uniacid']));
			plog('merchant.level.edit', "修改招商中心等级 ID: {$id}");
		} else {
			pdo_insert('sz_yi_merchant_level', $data);
			$id = pdo_insertid();
			plog('merchant.level.add', "添加招商中心等级 ID: {$id}");
		}
		message('更新等级成功！', $this->createPluginWebUrl('merchant/level', array('op' => 'display')), 'success');
	}
} elseif ($operation == 'delete') {
	$id = intval($_GPC['id']);
	$level = pdo_fetch("SELECT id,level_name FROM " . tablename('sz_yi_merchant_level') . " WHERE id = '$id'");
	if (empty($level)) {
		message('抱歉，等级不存在或是已经被删除！', $this->createPluginWebUrl('merchant/level', array('op' => 'display')), 'error');
	}
	pdo_delete('sz_yi_merchant_level', array('id' => $id, 'uniacid' => $_W['uniacid']));
	plog('merchant.level.delete', "删除招商中心等级 ID: {$id} 等级名称: {$level['levelname']}");
	message('等级删除成功！', $this->createPluginWebUrl('merchant/level', array('op' => 'display')), 'success');
}
load()->func('tpl');
include $this->template('level');
