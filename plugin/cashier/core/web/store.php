<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;

$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$plugin_bonus = p('bonus');
$plugin_commission = p('commission');
$trade     = m('common')->getSysset('trade');
if ($plugin_commission) {
    $com_set = p('commission')->getSet();
}
if ($operation == 'display') {
    ca('cashier.store.view');
    $page      = max(1, intval($_GPC['page']));
    $pagesize  = 20;
    $condition = ' uniacid = :uniacid';
    $params    = array(':uniacid' => $_W['uniacid']);
    if (!empty($_GPC['keyword'])) {
        $condition .= ' AND name LIKE :name OR contact LIKE :contact OR mobile LIKE :mobile OR address LIKE :address';
        $_GPC['keyword']    = trim($_GPC['keyword']);
        $params[':name']    = '%' . trim($_GPC['keyword']) . '%';
        $params[':contact'] = '%' . trim($_GPC['keyword']) . '%';
        $params[':mobile']  = '%' . trim($_GPC['keyword']) . '%';
        $params[':address'] = '%' . trim($_GPC['keyword']) . '%';
    }
    $sql   = 'SELECT * FROM ' . tablename('sz_yi_cashier_store') . " where 1 and {$condition} ORDER BY id DESC LIMIT " . ($page - 1) * $pagesize . ',' . $pagesize;
    $list  = pdo_fetchall($sql, $params);
    $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('sz_yi_cashier_store') . " where 1 and {$condition}", $params);
    $pager = pagination($total, $page, $pagesize);
} elseif ($operation == 'post') {
    $id = intval($_GPC['id']);
    if (empty($id)) {
        ca('cashier.store.add');
    } else {
        ca('cashier.store.view|cashier.store.edit');
    }
    $pcoupon = p('coupon');
    if ($pcoupon) {
        $couponList = pdo_fetchall('SELECT id, couponname FROM ' . tablename('sz_yi_coupon') . 'WHERE uniacid = :uniacid', array(
            ':uniacid' => $_W['uniacid']
        ));
    }
    if (checksubmit('submit')) {
        $data = array(
            'uniacid' => $_W['uniacid'],
            'name'    => trim($_GPC['name']),
            'thumb'   => trim($_GPC['thumb']),
            'contact' => trim($_GPC['contact']),
            'mobile'  => trim($_GPC['mobile']),
            'province'  => trim($_GPC['province']),
            'city'  => trim($_GPC['city']),
            'area'  => trim($_GPC['area']),
            'street' => trim($_GPC['street']),
            'address' => trim($_GPC['address']),
            'member_id' => trim($_GPC['member_id']),
            'deduct_credit1' => trim($_GPC['deduct_credit1']),
            'deduct_credit2' => trim($_GPC['deduct_credit2']),
            'settle_platform' => trim($_GPC['settle_platform']),
            'settle_store' => trim($_GPC['settle_store']),
            'commission1_rate' => trim($_GPC['commission1_rate']),
            'commission2_rate' => trim($_GPC['commission2_rate']),
            'commission3_rate' => trim($_GPC['commission3_rate']),
            'credit1' => trim($_GPC['credit1']),
            'creditpack' => trim($_GPC['creditpack']),
            'redpack_min' => trim($_GPC['redpack_min']),
            'redpack' => trim($_GPC['redpack']),
            'deredpack' => trim($_GPC['deredpack']),
            'decommission' => trim($_GPC['decommission']),
            'debonus' => trim($_GPC['debonus']),
            'decredits' => trim($_GPC['decredits']),
            'iscontact' => intval($_GPC['iscontact']),
            'condition' => trim($_GPC['condition']),
            'isreturn' => intval($_GPC['isreturn']),
            'centercan' => intval($_GPC['centercan'])
        );
        if ($plugin_bonus) {
            $data['bonus'] = floatval($_GPC['bonus']);
        }
        if ($pcoupon) {
            $data['coupon_id'] = trim($_GPC['coupon_id']);
        }
        if (!empty($id)) {
            $cashier_stores = pdo_fetch(' select * from ' .tablename('sz_yi_cashier_store'). ' where id='.$id);
            $oldopenid = pdo_fetchcolumn('select openid from ' .tablename('sz_yi_member'). ' where id='.$cashier_stores['member_id']);
            if ($cashier_stores['member_id'] != $data['member_id']) {
                $msg = array(
                                'keyword1' => array('value' => '收银台商户通知', 'color' => '#73a68d'),
                                'keyword2' => array('value' => '【商户名称】' . $cashier_stores['name'], 'color' => '#73a68d'),
                                'remark' => array('value' => '您于此商户绑定的微信角色已经被替换!')
                            );
                $m_msg = array(
                                'keyword1' => array('value' => '收银台商户通知', 'color' => '#73a68d'),
                                'keyword2' => array('value' => '【商户名称】' . $cashier_stores['name'], 'color' => '#73a68d'),
                                'remark' => array('value' => '此商户绑定的微信角色已经成功更换成您!')
                            );          
                m('message')->sendCustomNotice($oldopenid, $msg);
                m('message')->sendCustomNotice($data['member_id'], $m_msg);
            }
            pdo_update('sz_yi_cashier_store', $data, array('id' => $id, 'uniacid' => $_W['uniacid']));
            plog('cashier.store.edit', "编辑商户 ID: {$id} <br/>店名: {$data['name']}");
            message('更新商户信息成功！', $this->createPluginWebUrl('cashier/store'), 'success');
        } else {
            $data['create_time'] = date('Y-m-d H:i:s');
            pdo_insert('sz_yi_cashier_store', $data);
            $m_msg = array(
                                'keyword1' => array('value' => '收银台商户通知', 'color' => '#73a68d'),
                                'keyword2' => array('value' => '【商户名称】' . $cashier_stores['name'], 'color' => '#73a68d'),
                                'remark' => array('value' => '您已经被绑定成为此商户的微信角色!')
                            );   
            $id = pdo_insertid();
            plog('cashier.store.add', "添加商户 ID: {$id}  <br/>店名: {$data['name']}");
            message('添加商户成功！', $this->createPluginWebUrl('cashier/store'), 'success');
        }
    }
    
    $item = pdo_fetch('SELECT * FROM ' . tablename('sz_yi_cashier_store') . ' WHERE id =:id and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $id));
    $member = pdo_fetch('SELECT id,nickname FROM ' . tablename('sz_yi_member') . " WHERE uniacid=:uniacid AND id=:id",
        array(':uniacid' => $_W['uniacid'], ':id' => $item['member_id'])
    );
} elseif ($operation == 'delete') {
    ca('cashier.store.delete');
    $id = intval($_GPC['id']);
    $item = pdo_fetch('SELECT id, name FROM ' . tablename('sz_yi_cashier_store') . ' WHERE id =:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));
    if (empty($item)) {
        message('抱歉，商户不存在或是已经被删除！', $this->createPluginWebUrl('cashier/store', array('op' => 'display')), 'error');
    }
    pdo_delete('sz_yi_cashier_store', array('id' => $id, 'uniacid' => $_W['uniacid']));
    plog('cashier.store.delete', "删除商户 ID: {$id}  <br/>商户名称: {$item['couponname']} ");
    message('商户删除成功！', $this->createPluginWebUrl('cashier/store', array('op' => 'display')), 'success');
} elseif ($operation == 'qrcode') {
    $id = intval($_GPC['id']);
    $item = pdo_fetch('SELECT * FROM ' . tablename('sz_yi_cashier_store') . ' WHERE id =:id and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $id));
    $accountDir = IA_ROOT . '/addons/sz_yi/data/qrcode/' . $_W['uniacid'];
    if (!is_dir($accountDir)) {
        load()->func('file');
        mkdirs($accountDir);
    }
    $payLink   = $_W['siteroot'] . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&m=sz_yi&do=plugin&p=cashier&method=order_confirm&sid=' . $id.'&mid='.$item['member_id'];
    $qrcodeImg = 'cashier_store_' . $id . '.png';
    $fullPath  = $accountDir . '/' . $qrcodeImg;
    if (!is_file($fullPath)) {
        require IA_ROOT . '/framework/library/qrcode/phpqrcode.php';
        QRcode::png($payLink, $fullPath, QR_ECLEVEL_H, 4);
    }

    header('Content-type: image/png'); 
    header("Content-Disposition: attachment; filename='$qrcodeImg'");
    readfile($fullPath);
    exit;
} elseif ($operation == 'getmembers') {
    global $_W, $_GPC;
    $keyword            = trim($_GPC['keyword']);
    $params             = array();
    $params[':uniacid'] = $_W['uniacid'];
    $condition = ' and uniacid=:uniacid';
    if (!empty($keyword)) {
        $condition .= ' AND `nickname` LIKE :keyword';
        $params[':keyword'] = "%{$keyword}%";
    }
    $members = pdo_fetchall('SELECT id,nickname,avatar FROM ' . tablename('sz_yi_member') . " WHERE 1 {$condition}", $params);
    include $this->template('getmembers');
    exit;
} elseif ($operation == 'waiter'){
    
    $id = $_GPC['id'];
    
    $page      = max(1, intval($_GPC['page']));
    $pagesize  = 20;
    $condition = ' uniacid = :uniacid';
    $params    = array(':uniacid' => $_W['uniacid']);
    $params[':sid'] = $id;
    if (!empty($_GPC['keyword'])) {
        $condition .= ' AND realname LIKE :realname  OR mobile LIKE :mobile ';
        $_GPC['keyword']    = trim($_GPC['keyword']);
        $params[':realname']    = '%' . trim($_GPC['keyword']) . '%';
        $params[':mobile'] = '%' . trim($_GPC['keyword']) . '%';
       
    }
    $sql   = 'SELECT * FROM ' . tablename('sz_yi_cashier_store_waiter') . " where 1 and {$condition} and sid = :sid ORDER BY id DESC LIMIT " . ($page - 1) * $pagesize . ',' . $pagesize;
    $list  = pdo_fetchall($sql, $params);
    $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('sz_yi_cashier_store_waiter') . " where 1 and {$condition} and sid = :sid ", $params);
    $pager = pagination($total, $page, $pagesize);    
} elseif ($operation == 'post_waiter'){
    $id = intval($_GPC['id']);
    if (empty($id)) {
        ca('cashier.store.add');
    } else {
        ca('cashier.store.view|cashier.store.edit');
    }
    $pcoupon = p('coupon');
    if ($pcoupon) {
        $couponList = pdo_fetchall('SELECT id, couponname FROM ' . tablename('sz_yi_coupon') . 'WHERE uniacid = :uniacid', array(
            ':uniacid' => $_W['uniacid']
        ));
    }
    if (checksubmit('submit')) {
        $data = array(
            'uniacid' => $_W['uniacid'],
            'realname'    => trim($_GPC['realname']),
            'mobile'   => trim($_GPC['mobile']),
            'member_id' => trim($_GPC['member_id']),
            'sid' => trim($_GPC['sid'])


        );
        $store = pdo_fetch( "select member_id from ".tablename('sz_yi_cashier_store')." where id = ".$_GPC['sid']);
        if($data['member_id'] == $store['member_id']){
            message('店员微信不能与管理员微信相同！', $this->createPluginWebUrl('cashier/store/post_waiter',array('id'=>$id)), 'error');
        }
        $waiter = pdo_fetchall(" select member_id from ".tablename('sz_yi_cashier_store_waiter')." where sid =".$_GPC['sid']);
        foreach($waiter as $value){
            if($value['member_id'] == $data['member_id']){
                 message('店员微信重复！', $this->createPluginWebUrl('cashier/store/post_waiter',array('id'=>$id)), 'error');
            }
        }
        if (!empty($id)) {
            $cashier_stores = pdo_fetch(' select * from ' .tablename('sz_yi_cashier_store_waiter'). ' where id='.$id);
            $oldopenid = pdo_fetchcolumn('select openid from ' .tablename('sz_yi_member'). ' where id='.$cashier_stores['member_id']);
            if($cashier_stores['member_id'] != $data['member_id']){
                $msg = array(
                                'keyword1' => array('value' => '收银台商户通知', 'color' => '#73a68d'),
                                'keyword2' => array('value' => '【商户名称】' . $cashier_stores['name'], 'color' => '#73a68d'),
                                'remark' => array('value' => '您于此商户绑定的微信角色已经被替换!')
                            );
                $m_msg = array(
                                'keyword1' => array('value' => '收银台商户通知', 'color' => '#73a68d'),
                                'keyword2' => array('value' => '【商户名称】' . $cashier_stores['name'], 'color' => '#73a68d'),
                                'remark' => array('value' => '此商户绑定的微信角色已经成功更换成您!')
                            );          
                m('message')->sendCustomNotice($oldopenid, $msg);
                m('message')->sendCustomNotice($data['member_id'], $m_msg);
            }
            $data['savetime'] = date('Y-m-d H:i:s');
            pdo_update('sz_yi_cashier_store_waiter', $data, array('id' => $id, 'uniacid' => $_W['uniacid']));
            plog('cashier.store.edit', "编辑商户 ID: {$id} <br/>店名: {$data['name']}");
            message('更新店员信息成功！', $this->createPluginWebUrl('cashier/store/waiter',array('id'=>$_GPC['sid'])), 'success');
        } else {
            $data['createtime'] = date('Y-m-d H:i:s');
            $data['savetime'] = date('Y-m-d H:i:s');
            pdo_insert('sz_yi_cashier_store_waiter', $data);
            $m_msg = array(
                                'keyword1' => array('value' => '收银台商户通知', 'color' => '#73a68d'),
                                'keyword2' => array('value' => '【商户名称】' . $cashier_stores['name'], 'color' => '#73a68d'),
                                'remark' => array('value' => '您已经被绑定成为此商户的微信角色!')
                            );   
            $id = pdo_insertid();
            plog('cashier.store.add', "添加店员 ID: {$id}  <br/>店员名: {$data['name']}");
            message('添加店员成功！', $this->createPluginWebUrl('cashier/store/waiter',array('id'=>$_GPC['sid'])), 'success');
        }
    }
    
    $item = pdo_fetch('SELECT * FROM ' . tablename('sz_yi_cashier_store_waiter') . ' WHERE id =:id and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $id));
    $member = pdo_fetch('SELECT id,nickname FROM ' . tablename('sz_yi_member') . " WHERE uniacid=:uniacid AND id=:id",
        array(':uniacid' => $_W['uniacid'], ':id' => $item['member_id'])
    );

} elseif ($operation == 'delete_waiter') {
    ca('cashier.store.delete');
    $id = intval($_GPC['id']);
    $sid = intval($_GPC['sid']);
    $item = pdo_fetch('SELECT id, realname FROM ' . tablename('sz_yi_cashier_store_waiter') . ' WHERE id =:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));
    if (empty($item)) {
        message('抱歉，店员不存在或是已经被删除！', $this->createPluginWebUrl('cashier/store', array('op' => 'waiter' , 'sid' => $sid)), 'error');
    }
    pdo_delete('sz_yi_cashier_store_waiter', array('id' => $id, 'uniacid' => $_W['uniacid']));
    plog('cashier.store.delete', "删除店员 ID: {$id}  <br/>店员名称: {$item['realname']} ");
    message('店员删除成功！', $this->createPluginWebUrl('cashier/store', array('op' => 'waiter' , 'id' => $sid)), 'success');
} 

include $this->template('store');
