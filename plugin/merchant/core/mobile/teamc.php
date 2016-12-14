<?php
global $_W, $_GPC;
$openid   = m('user')->getOpenid();
$member = m('member')->getMember($openid);
if ($_W['isajax']) {
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$center = $this->model->isCenter($openid);
	$sql = "select * from " . tablename('sz_yi_merchants') . " where uniacid={$_W['uniacid']} and center_id={$center['id']} ORDER BY id DESC ";
    $sql .= "LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
    $list = pdo_fetchall($sql);
    foreach ($list as &$value) {
        $merchants_member = m('member')->getMember($value['openid']);
        $value['avatar'] = $merchants_member['avatar'];
        $value['realname'] = $merchants_member['realname'];
    }
    unset($value);
	return show_json(1, array('list' => $list, 'pagesize' => $psize));
}
include $this->template('teamc');
