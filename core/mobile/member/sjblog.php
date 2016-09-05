<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$type      = $_GPC['type'];
$openid    = m('user')->getOpenid();
$uniacid   = $_W['uniacid'];
$member    = m('member')->getInfo($openid);
if ($_W['isajax']) {
	if ($operation == 'display') {
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$condition = ' and uid=:uid and uniacid=:uniacid ';
		$params = array(':uniacid' => $uniacid, ':uid' => $member['uid']);
	}
	$list = pdo_fetchall('select * from ' . tablename('sz_yi_sjb_log') . " where 1 {$condition}  order by createtime desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
	$total = pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_sjb_log') . " where 1 {$condition}", $params);

	show_json(1, array('total' => $total, 'list' => $list, 'pagesize' => $psize));
}
include $this->template('member/sjblog');

