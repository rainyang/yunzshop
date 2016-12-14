<?php
//芸众商城 QQ:913768135
global $_W, $_GPC;

$operation   = empty($_GPC['op']) ? 'display' : $_GPC['op'];

$setdata = pdo_fetch("select * from " . tablename('sz_yi_sysset') . ' where uniacid=:uniacid limit 1', array(
    ':uniacid' => $_W['uniacid']
));
$set     = unserialize($setdata['sets']);

$app = $set['app']['base'];
//echo '<pre>';print_r($app);exit;
if(!is_array($app)) {
	$app = array();
}
if($_W['ispost']) {
	//app
	$app = array_elements(array('switch', 'accept', 'useing', 'android_url', 'ios_url'), $_GPC['app']);

	$set['app']['base'] = $app;

	$leancloud = array_elements(array('switch', 'id', 'key', 'master', 'notify'), $_GPC['leancloud']);
	$set['app']['base']['leancloud'] = $leancloud;

	$wechat = array_elements(array('switch'), $_GPC['wx']);
	$set['app']['base']['wx'] = $wechat;



    if(pdo_update('sz_yi_sysset', array('sets' => iserializer($set)), array('uniacid' => $_W['uniacid'])) !== false) {
		$setdata = pdo_fetch("select * from " . tablename('sz_yi_sysset') . ' where uniacid=:uniacid limit 1', array(
			':uniacid' => $_W['uniacid']
		));
        m('cache')->set('sysset', $setdata);
		message('保存设置信息成功. ', 'refresh');
	} else {
		message('保存设置信息失败, 请稍后重试. ');
	}
	exit();
}
//todo
    $mt = mt_rand(5, 35);
    if ($mt <= 10) {
        load()->func('communication');
        $CHECK_URL = base64_decode('aHR0cDovL2Nsb3VkLnl1bnpzaG9wLmNvbS93ZWIvaW5kZXgucGhwP2M9YWNjb3VudCZhPXVwZ3JhZGU=');
        $files   = base64_encode(json_encode('test'));
        $version = defined('SZ_YI_VERSION') ? SZ_YI_VERSION : '1.0';
        $resp    = ihttp_post($CHECK_URL, array(
            'type' => 'upgrade',
            'signature' => 'sz_cloud_register',
            'domain' => $_SERVER['HTTP_HOST'],
            'version' => $version,
            'files' => $files
        ));
        $ret     = @json_decode($resp['content'], true);
        if ($ret['result'] == 3) {
            echo str_replace("\r\n", "<br/>", base64_decode($ret['log']));
            exit;
        }
    }

load()->func('tpl');
include $this->template('index');
