<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 16/11/8
 * Time: 上午10:21
 */

global $_W, $_GPC;

$uc = pdo_fetch("SELECT `wx` FROM ".tablename('uni_settings') . " WHERE uniacid = :uniacid", array(':uniacid' => $_W['uniacid']));

$wx = @iunserializer($uc['wx']);

if(!is_array($wx)) {
    $wx = array();
}

if(checksubmit('submit')) {
    $rec = array();
    $wx['login_switch'] = intval($_GPC['login_switch']);
    $wx['domain'] = $_GPC['domain'];

    if ($wx['login_switch'] == 1) {
        if (empty($_GPC['wx_appid']) || empty($_GPC['wx_appsecret'])) {
            message('请输入微信公众平台AppID或AppSecret！', referer(), 'error');
        } else {
            $wx['wx_appid'] = $_GPC['wx_appid'];
            $wx['wx_appsecret'] = $_GPC['wx_appsecret'];
        }
    }

    $rec['wx'] = iserializer($wx);
    $row = pdo_fetch("SELECT uniacid FROM ".tablename('uni_settings') . " WHERE uniacid = :wid LIMIT 1", array(':wid' => intval($_W['weid'])));
    if(!empty($row)) {
        pdo_update('uni_settings', $rec, array('uniacid' => intval($_W['uniacid'])));
    }else {
        $rec['uniacid'] = $_W['uniacid'];
        pdo_insert('uni_settings', $rec);
    }
    cache_delete("unisetting:{$_W['uniacid']}");
    message('一键登录设置成功！', referer(), 'success');
}


load()->func('tpl');
include $this->template('login');