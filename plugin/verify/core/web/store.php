<?php
//芸众商城 QQ:913768135
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;

$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') {
    ca('verify.store.view');
    $sql = 'SELECT * FROM ' . tablename('sz_yi_store_category') . ' WHERE `uniacid` = :uniacid ORDER BY `parentid`, `displayorder` DESC';
    $category = pdo_fetchall($sql, array(':uniacid' => $_W['uniacid']), 'id');
    $result = pdo_fetchall("SELECT uid,realname,username FROM " . tablename('sz_yi_perm_user') . ' where uniacid =' . $_W['uniacid']);

    $parent = $children = array();
    if (!empty($category)) {
        foreach ($category as $cid => $cate) {
            if (!empty($cate['parentid'])) {
                $children[$cate['parentid']][] = $cate;
            } else {
                $parent[$cate['id']] = $cate;
            }
        }
    }

    $params             = array();
    $params[':uniacid'] = $_W['uniacid'];
    $condition          = " and uniacid=:uniacid";
    if (!empty($_GPC['category']['parentid'])) {
        $condition .= " AND pcate = :pcate";
        $params[':pcate'] = intval($_GPC['category']['parentid']);
    }
    if (!empty($_GPC['category']['childid'])) {
        $condition .= " AND ccate = :ccate";
        $params[':ccate'] = intval($_GPC['category']['childid']);
    }
    if (!empty($_GPC['province'])) {
        $condition .= " AND province = :province";
        $params[':province'] = $_GPC['province'];
    }
    if (!empty($_GPC['city'])) {
        $condition .= " AND city = :city";
        $params[':city'] = $_GPC['city'];
    }
    if (!empty($_GPC['area'])) {
        $condition .= " AND area = :area";
        $params[':area'] = $_GPC['area'];
    }
    if (!empty($_GPC['street'])) {
        $condition .= " AND street = :street";
        $params[':street'] = $_GPC['street'];
    }
    if (!empty($_GPC['keyword'])) {
        $condition .= " AND storename like '%{$_GPC['keyword']}%'";
    }
    $list = pdo_fetchall("SELECT * FROM " . tablename('sz_yi_store') . " WHERE 1 {$condition} ORDER BY id asc", $params);
    foreach ($list as &$row) {
        $row['salercount'] = pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_saler') . ' where storeid=:storeid limit 1', array(
            ':storeid' => $row['id']
        ));
        $row['address'] = $row['province'].$row['city'].$row['area'].$row['street'].$row['address'];
    }
    unset($row);
} elseif ($operation == 'post') {
    $myself_support = !empty($_GPC['myself_support']) ? $_GPC['myself_support'] : '0';
    $id = intval($_GPC['id']);
    if (empty($id)) {
        ca('verify.store.add');
    } else {
        ca('verify.store.view|verify.store.edit');
    }
    $sql = 'SELECT * FROM ' . tablename('sz_yi_store_category') . ' WHERE `uniacid` = :uniacid ORDER BY `parentid`, `displayorder` DESC';
    $category = pdo_fetchall($sql, array(':uniacid' => $_W['uniacid']), 'id');
    $result = pdo_fetchall("SELECT uid,realname,username FROM " . tablename('sz_yi_perm_user') . ' where uniacid =' . $_W['uniacid']);

    $parent = $children = array();
    if (!empty($category)) {
        foreach ($category as $cid => $cate) {
            if (!empty($cate['parentid'])) {
                $children[$cate['parentid']][] = $cate;
            } else {
                $parent[$cate['id']] = $cate;
            }
        }
    }
    $item = pdo_fetch("SELECT * FROM " . tablename('sz_yi_store') . " WHERE id =:id and uniacid=:uniacid limit 1", array(
        ':uniacid' => $_W['uniacid'],
        ':id' => $id
    ));
    if (p('cashier')) {
        $cashier = pdo_fetchall(" SELECT id,name FROM " .tablename('sz_yi_cashier_store'). " WHERE uniacid=:uniacid ", array(':uniacid' => $_W['uniacid']));
    }

    if (p('supplier')) {
        $supplier = p('supplier')->AllSuppliers();
    }

    $member = pdo_fetch('SELECT id,nickname FROM ' . tablename('sz_yi_member') . " WHERE uniacid=:uniacid AND id=:id",
        array(':uniacid' => $_W['uniacid'], ':id' => $item['member_id'])
    );
    if (checksubmit('submit')) {
        if ($_GPC['iswithcashier'] == 1 && p('cashier')) {
            $data_cashier = array(
                'uniacid' => $_W['uniacid'],
                'name' => trim($_GPC['storename']),
                'member_id' => intval($_GPC['member_id']),

            );
            pdo_insert('sz_yi_cashier_store', $data_cashier);
            $cashier_id = pdo_insertid();

        }
        $data = array(
            'uniacid' => $_W['uniacid'],
            'storename' => trim($_GPC['storename']),
            'address' => trim($_GPC['address']),
            'member_id' => intval($_GPC['member_id']),
            'tel' => trim($_GPC['tel']),
            'lng' => $_GPC['map']['lng'],
            'lat' => $_GPC['map']['lat'],
            'status' => intval($_GPC['status']),
            'myself_support' => intval($myself_support),
            'balance' => intval($_GPC['balance']),
            'pcate' => intval($_GPC['category']['parentid']),
            'ccate' => intval($_GPC['category']['childid']),
            'tcate' => intval($_GPC['category']['thirdid']),
            'singleprice' => $_GPC['singleprice'],
            'info' => $_GPC['info'],
            'thumb' => $_GPC['thumb'],
            'supplier_uid' => $_GPC['supplier_uid']
        );
        if (p('cashier')) {
            if (!empty($cashier_id)) {
                $data['cashierid'] = $cashier_id;
            } elseif (!empty($_GPC['cashierid'])) {
                $is_cashier = pdo_fetchcolumn(" SELECT id FROM " .tablename('sz_yi_store'). " WHERE cashierid=:id and uniacid=:uniacid limit 1", array(':id' => intval($_GPC['cashierid']), ':uniacid' => $_W['uniacid']));
                if (empty($is_cashier)) {
                    $data['cashierid'] = intval($_GPC['cashierid']);
                } elseif ($is_cashier != $id) {
                    message('此收银台已被其他门店绑定，请选择其他收银台！', $this->createPluginWebUrl('verify/store', array(
                        'op' => 'post',
                        'id' => $id
                    )), 'error');
                }
            }
        }

        $data["province"] = trim($_GPC["province"]);
        $data["city"] = trim($_GPC["city"]);
        $data["area"] = trim($_GPC["area"]);
        $data["street"] = trim($_GPC["street"]);
        if (!empty($id)) {
            pdo_update('sz_yi_store', $data, array(
                'id' => $id,
                'uniacid' => $_W['uniacid']
            ));
            plog('verify.store.edit', "编辑核销门店 ID: {$id}");
        } else {
            pdo_insert('sz_yi_store', $data);
            $id = pdo_insertid();
            plog('verify.store.add', "添加核销门店 ID: {$id}");
        }
        message('更新门店成功！', $this->createPluginWebUrl('verify/store', array(
            'op' => 'display'
        )), 'success');
    }
} elseif ($operation == 'delete') {
    ca('verify.store.delete');
    $id   = intval($_GPC['id']);
    $item = pdo_fetch("SELECT id,storename FROM " . tablename('sz_yi_store') . " WHERE id = '$id'");
    if (empty($item)) {
        message('抱歉，门店不存在或是已经被删除！', $this->createPluginWebUrl('verify/store', array(
            'op' => 'display'
        )), 'error');
    }
    pdo_delete('sz_yi_store', array(
        'id' => $id,
        'uniacid' => $_W['uniacid']
    ));
    plog('verify.store.delete', "删除核销门店 ID: {$id} 门店名称: {$item['storename']}");
    message('门店删除成功！', $this->createPluginWebUrl('verify/store', array(
        'op' => 'display'
    )), 'success');
} elseif ($operation == 'query') {
    $kwd                = trim($_GPC['keyword']);
    $params             = array();
    $params[':uniacid'] = $_W['uniacid'];
    $condition          = " and uniacid=:uniacid";
    if (!empty($kwd)) {
        $condition .= " AND `storename` LIKE :keyword";
        $params[':keyword'] = "%{$kwd}%";
    }
    $ds = pdo_fetchall('SELECT id,storename FROM ' . tablename('sz_yi_store') . " WHERE 1 {$condition} order by id asc", $params);
    include $this->template('query_store');
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
}
load()->func('tpl');
include $this->template('store');
