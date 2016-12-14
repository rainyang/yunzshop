<?php
global $_W, $_GPC;
$openid   	= m('user')->getOpenid();
$mychannels = $this->model->getInfo($openid);
$mychannels = $mychannels['channel']['mychannels'];
$total 		= count($mychannels);
$ids 		= array();
foreach ($mychannels as $key => $value) {
    $ids[] = $value['id'];
}
$ids = implode(',', $ids);
if ($_W['isajax']) {
	$pindex = max(1, intval($_GPC['page']));
	$psize 	= 20;
	$list 	= array();
	if (!empty($ids)) {
    	$list 	= pdo_fetchall("SELECT * FROM " . tablename('sz_yi_member') . " where id in ({$ids}) and uniacid = " . $_W['uniacid'] . " ORDER BY id desc limit " . ($pindex - 1) * $psize . ',' . $psize);
		foreach ($list as &$row) {
	        $row['channel_level_name'] = pdo_fetchcolumn("SELECT level_name FROM " . tablename('sz_yi_channel_level') . " where uniacid={$_W['uniacid']} and id={$row['channel_level']}");
		}
		unset($row);
	}
	return show_json(1, array('list' => $list, 'pagesize' => $psize));
}
include $this->template('team');
