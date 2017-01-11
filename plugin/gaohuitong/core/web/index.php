<?php
//芸众商城 QQ:913768135
global $_W, $_GPC;

$ght= pdo_fetch("select * from " . tablename('sz_yi_gaohuitong') . ' where uniacid=:uniacid limit 1', array(
    ':uniacid' => $_W['uniacid']
));

if($_W['ispost']) {
	//app
	$data = $_GPC['ght'];
    $data['uniacid'] = $_W['uniacid'];

    if (empty($ght)) {
        if (pdo_insert('sz_yi_gaohuitong', $data) === false) {
            message('保存设置信息失败, 请稍后重试. ');
        }
    } else {
        if(pdo_update('sz_yi_gaohuitong', $data, array('uniacid' => $_W['uniacid'])) === false) {
            message('保存设置信息失败, 请稍后重试. ');
        }
    }

    message('保存设置信息成功. ', 'refresh');
}

load()->func('tpl');
include $this->template('index');
