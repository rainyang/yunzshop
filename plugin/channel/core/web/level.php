<?php
global $_W, $_GPC;

$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$set = $this->getSet();
if ($operation == 'display') {
	
    ca('channel.level.view');
    $list = pdo_fetchall("SELECT * FROM " . tablename('sz_yi_channel_level') . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY level_num asc");
} else if ($operation == 'post') {
	$id = intval($_GPC['id']);
	if (empty($id)) {
		ca('channel.level.add');
	} else {
		ca('channel.level.view|channel.level.edit');
	}
	$level = pdo_fetch("SELECT * FROM " . tablename('sz_yi_channel_level') . " WHERE id = '$id'");
	if (!empty($level['goods_id'])) {
		$goods = pdo_fetch('SELECT id,title FROM ' .tablename('sz_yi_goods') . ' WHERE id = :id', array(':id' => $level['goods_id']));
	}
	if (checksubmit('submit')) {
		if (empty($_GPC['level_name'])) {
			message('抱歉，请输入等级名称！');
		}
		if ($_GPC['level_num'] == "" ) {
			message('抱歉，请输入等级权重！');
		}
		if (empty($_GPC['purchase_discount'])) {
			message('抱歉，请输入进货折扣！');
		}
		if (empty($_GPC['min_price'])) {
			message('抱歉，请输入最小进货量！');
		}
		if (empty($_GPC['profit_sharing'])) {
			message('抱歉，请输入利润分成！');
		}
		$data = array(
			'uniacid' => $_W['uniacid'], 
			'level_name' => trim($_GPC['level_name']),
			'level_num' => intval($_GPC['level_num']), 
			'purchase_discount' => intval($_GPC['purchase_discount']), 
			'min_price' => intval($_GPC['min_price']), 
			'profit_sharing' => intval($_GPC['profit_sharing']),
			'become' => $set['become'],
			'team_count' => intval($_GPC['team_count']),
			'order_money' => $_GPC['order_money'],
			'order_count' => intval($_GPC['order_count']),
			'goods_id' => intval($_GPC['goods_id']),
			);
		if (!empty($id)) {
			$data['updatetime'] = time();
			pdo_update('sz_yi_channel_level', $data, array('id' => $id, 'uniacid' => $_W['uniacid']));
			plog('channel.level.edit', "修改渠道商等级 ID: {$id}");
		} else {
			$data['createtime'] = time();
			pdo_insert('sz_yi_channel_level', $data);
			$id = pdo_insertid();
			plog('channel.level.add', "添加渠道商等级 ID: {$id}");
		}
		message('更新等级成功！', $this->createPluginWebUrl('channel/level', array('op' => 'display')), 'success');
	}
} else if ($operation == 'delete') {
	ca('channel.level.delete');
	$id = intval($_GPC['id']);
    if($id){
	  $level = pdo_fetch("SELECT id,level_name FROM " . tablename('sz_yi_channel_level') . " WHERE uniacid =:uniacid and id=:id", array(':uniacid' => $_W['uniacid'], ':id' =>$id));
    }
    if (empty($level)) {
		message('抱歉，等级不存在或是已经被删除！', $this->createPluginWebUrl('channel/level', array('op' => 'display')), 'error');
	}
	pdo_delete('sz_yi_channel_level', array('id' => $id, 'uniacid' => $_W['uniacid']));
	plog('channel.level.delete', "删除渠道商等级 ID: {$id} 等级名称: {$level['level_name']}");
	message('等级删除成功！', $this->createPluginWebUrl('channel/level', array('op' => 'display')), 'success');
}
load()->func('tpl');
include $this->template('level');