<?php
//芸众商城 QQ:913768135
if (!defined('IN_IA')) {
	exit('Access Denied');
}
global $_W, $_GPC;
$set = $this->model->getSet();
$pindex = max(1, intval($_GPC['page']));
$psize = 20;
$condition = ' d.uniacid = :uniacid';
$params = array(':uniacid' => $_W['uniacid']);
if (!empty($_GPC['realname'])) {
	$_GPC['realname'] = trim($_GPC['realname']);
	$condition .= ' AND ( m.realname like :realname or m.nickname like :realname or m.mobile like :realname)';
	$params[':realname'] = "%{$_GPC['realname']}%";
}
$sql = 'SELECT d.*, m.nickname,m.avatar,m.realname,m.mobile FROM ' . tablename('sz_yi_card_data') . ' d ' . ' LEFT JOIN ' . tablename('sz_yi_member') . ' m ON m.openid = d.openid AND m.uniacid = d.uniacid ' . " WHERE  1 AND {$condition} AND bindtime<>0 ORDER BY bindtime DESC";
if (empty($_GPC['export'])) {
	$sql .= ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
}
$list = pdo_fetchall($sql, $params);
foreach ($list as &$row) {
	$row['bindtime'] = date('Y-m-d H:i', $row['bindtime']);
}
unset($row);
if ($_GPC['export'] == 1) {
	ca('coupon.log.export');
	$columns = array(
		array('title' => '序列号', 'field' => 'cdkey', 'width' => 20), 
		array('title' => '会员信息', 'field' => 'nickname', 'width' => 12), 
		array('title' => '姓名', 'field' => 'realname', 'width' => 12), 
		array('title' => '手机号', 'field' => 'mobile', 'width' => 12),
		array('title' => '绑定时间', 'field' => 'bindtime', 'width' => 30)
	);
	m('excel')->export($list, array('title' => "{$set['gift_title']}数据-" . date('Y-m-d-H-i', time()), 'columns' => $columns));
}
$total = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('sz_yi_card_data') . " WHERE 1 AND uniacid=:uniacid AND bindtime<>0", array(':uniacid' => $_W['uniacid']));
$pager = pagination($total, $pindex, $psize);
load()->func('tpl');
include $this->template('log');
