<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 16/11/2
 * Time: 下午2:24
 */
global $_W, $_GPC;

$uc = pdo_fetch("SELECT `uc`,`passport` FROM ".tablename('uni_settings') . " WHERE uniacid = :uniacid", array(':uniacid' => $_W['uniacid']));

$uc = @iunserializer($uc['uc']);
if(!is_array($uc)) {
    $uc = array();
}

if(checksubmit('submit')) {
    $rec = array();
    $uc['syn_member'] = intval($_GPC['syn_member']);
    $uc['syn_credit'] = intval($_GPC['syn_credit']);
    $uc['syn_group'] = intval($_GPC['syn_group']);


    $rec['uc'] = iserializer($uc);
    $row = pdo_fetch("SELECT uniacid FROM ".tablename('uni_settings') . " WHERE uniacid = :wid LIMIT 1", array(':wid' => intval($_W['weid'])));
    if(!empty($row)) {
        pdo_update('uni_settings', $rec, array('uniacid' => intval($_W['uniacid'])));
    }else {
        $rec['uniacid'] = $_W['uniacid'];
        pdo_insert('uni_settings', $rec);
    }
    cache_delete("unisetting:{$_W['uniacid']}");
    message('数据同步设置成功！', referer(), 'success');
}

load()->func('tpl');
include $this->template('syn');