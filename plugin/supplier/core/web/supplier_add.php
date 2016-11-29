<?php
global $_W, $_GPC;
//查询供应商角色权限id
$permid = $this->model->getSupplierPermId();
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
load()->model('user');
if($operation == 'post'){
    $id = $_GPC['id'];
    if (empty($id)) {
        ca('supplier.supplier.add');
    } else {
        ca('supplier.supplier.view|supplier.supplier.edit');
    }
    $su_info = pdo_fetch('select * from ' . tablename('sz_yi_perm_user') . ' where uniacid=:uniacid and id=:id' , array(
        ':uniacid' => $_W['uniacid'],
        ':id' => $id
        ));
    if (!empty($su_info)) {
        if ($su_info['uid'] == $_W['uid']) {
            message('无法修改自己的权限！', referer(), 'error');
        }
    }
    if ($_W['isajax'] && $_W['ispost']) {
        $data = array(
            'uniacid' => $_W['uniacid'], 
            'username' => trim($_GPC['username']), 
            'roleid' => $permid, 
            'status' => 1, 
            'perms' => is_array($_GPC['perms']) ? implode(',', $_GPC['perms']) : '');
        if (!empty($su_info['id'])) {
            $pwd = array();
            $result = pdo_fetch('select * from ' . tablename('users') . ' where uid=:uid' , array(
                    ':uid' => $su_info['uid']
                    ));
            $pwd['password'] = user_hash($_GPC['password'], $result['salt']);
            pdo_update('users', $pwd, array('uid' => $su_info['uid']));
            pdo_update('sz_yi_perm_user', $pwd, array('uid' => $su_info['uid'], 'uniacid' => $_W['uniacid']));
            plog('perm.user.edit', "编辑操作员 ID: {$su_info['uid']} 用户名: {$data['username']} ");
        } else {
            $result = pdo_fetch('select * from ' . tablename('users') . " where username='".$data['username']."'");
            if (!empty($result)) {
                die(json_encode(array('result' => 0, 'message' => '此用户为系统存在用户，无法添加')));
            } else {
                $data['uid'] = user_register(array('username' => $data['username'], 'password' => $_GPC['password']));
                $pwd = pdo_fetch('select password from ' . tablename('users') . ' where uid=:uid' , array(
                    ':uid' => $data['uid']
                    ));
                $data['password'] = $pwd['password'];
                pdo_insert('uni_account_users', array('uid' => $data['uid'], 'uniacid' => $data['uniacid'], 'role' => 'operator'));
                pdo_insert('sz_yi_perm_user', $data);
                $insert = array(
                    'uniacid'       => $_W['uniacid'],
                    'uid'           => $data['uid'],
                    'type'          => 'sz_yi',
                    'permission'    => 'sz_yi_menu_shop|sz_yi_menu_order|sz_yi_menu_plugins'
                );
                pdo_insert('users_permission', $insert);
                $id = pdo_insertid();
                plog('perm.user.add', "添加操作员 ID: {$id} 用户名: {$data['username']} ");
            }
            
        }
        die(json_encode(array('result' => 1)));
    }
} elseif ($operation == 'delete') {
    ca('supplier.supplier.delete');
    $id = intval($_GPC['id']);
    $item = pdo_fetch('SELECT * FROM ' . tablename('sz_yi_perm_user') . " WHERE id = '$id'");
    if (empty($item)) {
        message('抱歉，操作员不存在或是已经被删除！', $this->createPluginWebUrl('supplier/supplier'), 'error');
    }
    pdo_delete('sz_yi_perm_user', array('id' => $id, 'uniacid' => $_W['uniacid']));
    pdo_delete('users', array('uid' => $item['uid']));
    pdo_delete('sz_yi_af_supplier', array('openid' => $item['openid'], 'uniacid' => $_W['uniacid']));
    plog('supplier.supplier.delete', "删除操作员 ID: {$id} 用户名: {$item['username']} ");
    message('操作员删除成功！', $this->createPluginWebUrl('supplier/supplier'), 'success');
}
load()->func('tpl');
include $this->template('supplier_add');