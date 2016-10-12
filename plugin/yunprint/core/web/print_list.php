<?php
global $_W, $_GPC;
$this -> backlists();
$id = intval($_GPC['id']);
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'print_list';
if ($op == 'print_post') {
    if ($id > 0) {
        $item = pdo_fetch('SELECT * FROM ' . tablename('sz_yi_yunprint_list') . ' WHERE uniacid = :uniacid AND id = :id', array(':uniacid' => $_W['uniacid'], ':id' => $id));
    }
    if (empty($item)) {
        $item = array('status' => 1, 'print_nums' => 1);
    }
    if (checksubmit('submit')) {
        $result = pdo_fetch("SELECT * FROM " . tablename('sz_yi_yunprint_list') . " WHERE uniacid=:uniacid AND status=:status", array(
                ':uniacid'  => $_W['uniacid'],
                ':status'   => 1
            ));
        $data = array();
        $data['status'] = intval($_GPC['status']);
        if ($result['id'] != $id) {
            if (!empty($result) && $data['status'] == 1) {
                message('只能启用一台打印机', '', 'error');
            }
        }
        $data['mode'] = intval($_GPC['mode']);
        $data['name'] = !empty($_GPC['name']) ? trim($_GPC['name']) : message('打印机名称不能为空', '', 'error');
        $data['print_no'] = !empty($_GPC['print_no']) ? trim($_GPC['print_no']) : message('机器号不能为空', '', 'error');
        $data['member_code'] = $_GPC['member_code'];
        $data['key'] = !empty($_GPC['key']) ? trim($_GPC['key']) : message('打印机key不能为空', '', 'error');
        $data['print_nums'] = intval($_GPC['print_nums']) ? intval($_GPC['print_nums']) : 1;
        if (!empty($_GPC['qrcode_link']) && (strexists($_GPC['qrcode_link'], 'http://') || strexists($_GPC['qrcode_link'], 'https://'))) {
            $data['qrcode_link'] = trim($_GPC['qrcode_link']);
        }
        $data['uniacid'] = $_W['uniacid'];
        $data['sid'] = $sid;
        if (!empty($item) && $id) {
            pdo_update('sz_yi_yunprint_list', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
        } else {
            pdo_insert('sz_yi_yunprint_list', $data);
        }
        message('更新打印机设置成功', $this->createPluginWebUrl('yunprint/print_list', array('op' => 'print_list')), 'success');
    }

} elseif ($op == 'print_list') {
    $data = pdo_fetchall('SELECT * FROM ' . tablename('sz_yi_yunprint_list') . ' WHERE uniacid = :uniacid ', array(':uniacid' => $_W['uniacid']));
} elseif ($op == 'print_del') {
    $id = intval($_GPC['id']);
    pdo_delete('sz_yi_yunprint_list', array('uniacid' => $_W['uniacid'], 'id' => $id));
    message('删除打印机成功', referer(), 'success');
}
include $this -> template('print_list');
?>