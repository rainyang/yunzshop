<?php
//芸众商城 QQ:913768135
global $_W, $_GPC;

$operation   = empty($_GPC['op']) ? 'display' : $_GPC['op'];

$setdata = pdo_fetch("select * from " . tablename('sz_yi_wxapp') . ' where uniacid=:uniacid limit 1', array(
    ':uniacid' => $_W['uniacid']
));

if($_W['ispost']) {
	//app
	$data = array('switch' => $_GPC['switch'], 'appid' => $_GPC['appid'], 'secret' => $_GPC['secret']);

    if ($data['switch'] == 1 && (empty($data['appid']) || empty($data['secret']))) {
        message('数据不完整请重新输入. ', 'refresh', 'error');
    }

    if (empty($setdata)) {
        $data['uniacid'] = $_W['uniacid'];
        if (pdo_insert('sz_yi_wxapp', $data)) {
            message('保存设置信息成功. ', 'refresh');
        } else {
            message('保存设置信息失败, 请稍后重试. ');
        }
    } else {
        if(pdo_update('sz_yi_wxapp', $data, array('uniacid' => $_W['uniacid'])) !== false) {
            $setdata = pdo_fetch("select * from " . tablename('sz_yi_wxapp') . ' where uniacid=:uniacid limit 1', array(
                ':uniacid' => $_W['uniacid']
            ));
            message('保存设置信息成功. ', 'refresh');
        } else {
            message('保存设置信息失败, 请稍后重试. ');
        }
    }

	exit();
}

load()->func('tpl');
include $this->template('index');
