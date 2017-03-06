<?php
global $_W, $_GPC;

$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$set = $this->getSet();
$leveltype = $set['leveltype'];
if ($operation == 'display') {
    ca('bonus.level.view');
    $list = pdo_fetchall("SELECT * FROM " . tablename('sz_yi_bonus_level') . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY level asc");
} elseif ($operation == 'post') {
	$id = intval($_GPC['id']);
	if (empty($id)) {
		ca('bonus.level.add');
	} else {
		ca('bonus.level.view|bonus.level.edit');
	}
	$level = pdo_fetch("SELECT * FROM " . tablename('sz_yi_bonus_level') . " WHERE id = '$id'");

	if (checksubmit('submit')) {
		if (empty($_GPC['levelname'])) {
			message('抱歉，请输入名称！');
		}
		$data = array(
			'uniacid' => $_W['uniacid'], 
			'level' => $_GPC['level'],
			'levelname' => $_GPC['levelname'], 
			'agent_money' => floatval($_GPC['agent_money']), 
			'commissionmoney' => $_GPC['commissionmoney'], 
			'ordermoney' => $_GPC['ordermoney'], 
			'ordercount' => intval($_GPC['ordercount']), 
			'downcount' => intval($_GPC['downcount']),
			'downcountlevel1' => intval($_GPC['downcountlevel1']),
			'downcountlevel2' => intval($_GPC['downcountlevel2']),
			'downcountlevel3' => intval($_GPC['downcountlevel3']),
			'content' => intval($_GPC['content']),
			'premier' => intval($_GPC['premier']),
			'pcommission' => floatval($_GPC['pcommission']),
			'msgtitle' => trim($_GPC['msgtitle']),
			'msgcontent' => trim($_GPC['msgcontent']),
			'status' => intval($_GPC['status'])
			);
		if (!empty($id)) {
			pdo_update('sz_yi_bonus_level', $data, array('id' => $id, 'uniacid' => $_W['uniacid']));
			plog('bonus.level.edit', "修改代理等级 ID: {$id}");
		} else {
			pdo_insert('sz_yi_bonus_level', $data);
			$id = pdo_insertid();
			plog('bonus.level.add', "添加代理等级 ID: {$id}");
		}
		message('更新等级成功！', $this->createPluginWebUrl('bonus/level', array('op' => 'display')), 'success');
	}
} elseif ($operation == 'delete') {
	ca('bonus.level.delete');
	$id = intval($_GPC['id']);
    if($id){
	  $level = pdo_fetch("SELECT id,levelname FROM " . tablename('sz_yi_bonus_level') . " WHERE uniacid =:uniacid and id=:id", array(':uniacid' => $_W['uniacid'], ':id' =>$id));
    }
    if (empty($level)) {
		message('抱歉，等级不存在或是已经被删除！', $this->createPluginWebUrl('bonus/level', array('op' => 'display')), 'error');
	}
	pdo_delete('sz_yi_bonus_level', array('id' => $id, 'uniacid' => $_W['uniacid']));
	plog('bonus.level.delete', "删除代理等级 ID: {$id} 等级名称: {$level['levelname']}");
	message('等级删除成功！', $this->createPluginWebUrl('bonus/level', array('op' => 'display')), 'success');
}
load()->func('tpl');
include $this->template('level');