<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
ca('hotel.prints');
$op      = empty($_GPC['op']) ? 'print_list' : trim($_GPC['op']);
$setdata = pdo_fetch("select * from " . tablename('sz_yi_sysset') . ' where uniacid=:uniacid limit 1', array(
    ':uniacid' => $_W['uniacid']
));
$set     = unserialize($setdata['sets']);
$oldset  = unserialize($setdata['sets']);

 if ($op == 'print_post') {
     $id = intval($_GPC['id']);
    if ($id > 0) {
        $item = pdo_fetch('SELECT * FROM ' . tablename('sz_yi_print_list') . ' WHERE uniacid = :uniacid AND id = :id', array(':uniacid' => $_W['uniacid'], ':id' => $id));
    }
    if (empty($item)) {
        $item = array('status' => 1);
    }
    if (checksubmit('submit')) {
        $data['status'] = intval($_GPC['status']);
        $data['type'] = intval($_GPC['type']);
        $data['name'] = !empty($_GPC['name']) ? trim($_GPC['name']) : message('打印机名称不能为空', '', 'error');
        $data['print_no'] = !empty($_GPC['print_no']) ? trim($_GPC['print_no']) : message('机器号不能为空', '', 'error');
        $data['member_code'] = $_GPC['member_code'];
        $data['key'] = !empty($_GPC['key']) ? trim($_GPC['key']) : message('打印机key不能为空', '', 'error');
        $data['uniacid'] = $_W['uniacid'];
        if (!empty($item) && $id) {
            pdo_update('sz_yi_print_list', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
        } else {
            pdo_insert('sz_yi_print_list', $data);
        }
        message('更新打印机设置成功',$this->createPluginWebUrl('hotel/prints', array('op' =>'print_list')), 'success');
    }
    include $this->template('print_post');exit;


}else if ($op == 'print_list') {
    $data = pdo_fetchall('SELECT * FROM ' . tablename('sz_yi_print_list') . ' WHERE uniacid = :uniacid ', array(':uniacid' => $_W['uniacid']));
}else if ($op == 'print_del') {
    $id = intval($_GPC['id']);
    pdo_delete('sz_yi_print_list', array('uniacid' => $_W['uniacid'], 'id' => $id));
    message('删除打印机成功', referer(), 'success');
}
load()->func('tpl');
include $this->template('print_list');
