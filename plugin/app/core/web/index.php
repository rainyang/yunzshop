<?php
//芸众商城 QQ:913768135
global $_W, $_GPC;

$operation   = empty($_GPC['op']) ? 'display' : $_GPC['op'];

$setdata = pdo_fetch("select * from " . tablename('sz_yi_sysset') . ' where uniacid=:uniacid limit 1', array(
    ':uniacid' => $_W['uniacid']
));
$set     = unserialize($setdata['sets']);

$app = $set['app']['base'];

$condition = '';
$s_uniacid = intval($_GPC['s_uniacid']);
if(!empty($s_uniacid)) {
	$condition =" AND a.`uniacid` = :uniacid";
	$pars[':uniacid'] = $s_uniacid;
}
if(empty($_W['isfounder'])) {
	$condition .= " AND a.`uniacid` IN (SELECT `uniacid` FROM " . tablename('uni_account_users') . " WHERE `uid`=:uid)";
	$pars[':uid'] = $_W['uid'];
}
if(!empty($_GPC['expiretime'])) {
	$expiretime = intval($_GPC['expiretime']);
	$condition .= " AND a.`uniacid` IN(SELECT uniacid FROM " .tablename('uni_account_users') . " WHERE role = 'owner' AND uid IN (SELECT uid FROM " .tablename('users'). " WHERE endtime > :time AND endtime < :endtime))";
	$pars[':time'] = time();
	$pars[':endtime'] = time()+86400*$expiretime;
}
if ($_GPC['type'] == '3') {
	$condition .= " AND b.type = 3";
} elseif($_GPC['type'] == '1') {
	$condition .= " AND b.type <> 3";
}
$sql = "SELECT * FROM ". tablename('uni_account'). " as a LEFT JOIN". tablename('account'). " as b ON a.default_acid = b.acid WHERE a.default_acid <> 0 {$condition} ORDER BY a.`rank` DESC, a.`uniacid` DESC ";
$list = pdo_fetchall($sql, $pars);

if(!is_array($app)) {
	$app = array();
}
if($_W['ispost']) {
	//app
	$app = array_elements(array('switch', 'platformid'), $_GPC['app']);

	$set['app']['base'] = $app;

    $setdata = pdo_fetch("select * from " . tablename('sz_yi_sysset') . ' where uniacid=:uniacid limit 1', array(
        ':uniacid' => $_W['uniacid']
    ));

    if(pdo_update('sz_yi_sysset', array('sets' => iserializer($set)), array('uniacid' => $_W['uniacid'])) !== false) {
        m('cache')->set('sysset', $setdata);
		message('保存设置信息成功. ', 'refresh');
	} else {
		message('保存设置信息失败, 请稍后重试. ');
	}
	exit();
}

load()->func('tpl');
include $this->template('index');
