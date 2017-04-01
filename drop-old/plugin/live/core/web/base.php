<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 16/12/8
 * Time: 下午3:46
 */

global $_W, $_GPC;

$setdata = pdo_fetch("SELECT * FROM " . tablename('sz_yi_live_base') . ' where uniacid=:uniacid limit 1', array(
    ':uniacid' => $_W['uniacid']
));


if($_W['ispost']) {
    $set = array(
        'conditions' => $_GPC['setdata']['conditions'],
        'is_check' => $_GPC['setdata']['is_check'],
        'uniacid' => $_W['uniacid']
    );

    if (empty($setdata)) {
        if (pdo_insert('sz_yi_live_base', $set) === FALSE) {
            message('保存设置信息失败, 请稍后重试. ');
        }
    } elseif (pdo_update('sz_yi_live_base', $set, array('uniacid' => $_W['uniacid'])) === FALSE) {
        message('保存设置信息失败, 请稍后重试. ');
    }

    message('保存设置信息成功. ', 'refresh');
    exit();
}

load()->func('tpl');
include $this->template('base');