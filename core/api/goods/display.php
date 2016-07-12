<?php
//$api->validate('username','password');
$_YZ->ca('shop.goods.view');
$pindex    = max(1, intval($_GPC['page']));
$psize     = 20;
$condition = ' WHERE `uniacid` = :uniacid AND `deleted` = :deleted';
$params    = array(
    ':uniacid' => $_W['uniacid'],
    ':deleted' => '0'
);
if (!empty($_GPC['keyword'])) {
    $_GPC['keyword'] = trim($_GPC['keyword']);
    $condition .= ' AND `title` LIKE :title';
    $params[':title'] = '%' . trim($_GPC['keyword']) . '%';
}

/*
if (!empty($para['category']['thirdid'])) {
    $condition .= ' AND (`tcate` = :tcate or tcates = :tcate)';
    $params[':tcate'] = intval($para['category']['thirdid']);
}
if (!empty($para['category']['childid'])) {
    $condition .= ' AND (`ccate` = :ccate or ccates = :ccate)';
    $params[':ccate'] = intval($para['category']['childid']);
}
if (!empty($para['category']['parentid'])) {
    $condition .= ' AND (`pcate` = :pcate or pcates = :pcate)' ;
    $params[':pcate'] = intval($para['category']['parentid']);
}


if (!empty($para['category2']['thirdid'])) {
    $condition .= ' AND (`tcate1` = :tcate2 or tcates = :tcate2)';
    $params[':tcate2'] = intval($para['category2']['thirdid']);
}
if (!empty($para['category2']['childid'])) {
    $condition .= ' AND (`ccate1` = :ccate2 or ccates = :ccate2)';
    $params[':ccate2'] = intval($para['category2']['childid']);
}
if (!empty($para['category2']['parentid'])) {
    $condition .= ' AND (`pcate1` = :pcate2 or pcates = :pcate2)' ;
    $params[':pcate2'] = intval($para['category2']['parentid']);
}*/


if ($_GPC["status"] != '') {
    $condition .= ' AND `status` = :status';
    $params[':status'] = intval($_GPC['status']);
}


$product_attr = $_GPC['product_attr'];

if ($product_attr) {
    $condition .= ' AND (';
    foreach ($product_attr as $k => $p_attr) {
        if ($k == 0) {
            $condition .= " `{$p_attr}` = 1";
        } else {
            $condition .= " OR `{$p_attr}` = 1";
        }
    }
    $condition .= ' )';
}

//供应商搜索
if (!empty($_GPC["supplier_uid"]) && $_GPC["supplier_uid"] != 9999) {
    $condition .= " AND `supplier_uid` = "."$_GPC[supplier_uid]";
}

if ($_GPC["supplier_uid"] == 9999) {
    $condition .= ' AND `supplier_uid` = 0';
}

if(p('supplier')){
    $suproleid = pdo_fetchcolumn('select id from' . tablename('sz_yi_perm_role') . ' where status1 = 1');
    $userroleid = pdo_fetchcolumn('select roleid from ' . tablename('sz_yi_perm_user') . ' where uid=:uid and uniacid=:uniacid',array(':uid' => $_W['uid'],':uniacid' => $_W['uniacid']));

    //Author:RainYang Date:2016-04-09 Content:修改供应商判断条件,有可能上面两个id都是空的情况,照成商品不显示
    if((!empty($userroleid)) && ($userroleid == $suproleid)){
        $sql = 'SELECT * FROM ' . tablename('sz_yi_goods') . $condition . ' and supplier_uid='.$_W['uid'].' ORDER BY `status` DESC, `displayorder` DESC,
                    `id` DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $sqls = 'SELECT COUNT(*) FROM ' . tablename('sz_yi_goods') . $condition . ' and supplier_uid='.$_W['uid'];
        $total = pdo_fetchcolumn($sqls, $params);
    }
    else{
        $sql = 'SELECT * FROM ' . tablename('sz_yi_goods') . $condition . ' ORDER BY `status` DESC, `displayorder` DESC,
                        `id` DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $sqls = 'SELECT COUNT(*) FROM ' . tablename('sz_yi_goods') . $condition;
        $total = pdo_fetchcolumn($sqls, $params);
    }
}else{
    $sql = 'SELECT * FROM ' . tablename('sz_yi_goods') . $condition . ' ORDER BY `status` DESC, `displayorder` DESC,
                    `id` DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
    $sqls = 'SELECT COUNT(*) FROM ' . tablename('sz_yi_goods') . $condition;
    $total = pdo_fetchcolumn($sqls, $params);
}
$list  = pdo_fetchall($sql, $params);
//$pager = pagination($total, $pindex, $psize);
dump($list);
$_YZ->returnSuccess($list);