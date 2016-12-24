<?php
if (!defined("IN_IA")) {
    print ("Access Denied");
}
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') {
    $id = $_GPC['id'];    
    $openid = pdo_fetchcolumn("SELECT openid FROM " . tablename('sz_yi_member') . " WHERE uniacid={$_W['uniacid']} AND id={$id}");
    ca('shop.goods.view');
    $pindex    = max(1, intval($_GPC['page']));
    $psize     = 20;
    $condition = ' WHERE cs.`uniacid` = :uniacid AND g.`deleted` = :deleted AND cs.openid = :openid';
    $params    = array(
        ':uniacid' => $_W['uniacid'],
        ':deleted' => '0',
        ':openid' => $openid
    );
    if (!empty($_GPC['keyword'])) {
        $_GPC['keyword'] = trim($_GPC['keyword']);
        $condition .= ' AND g.`title` LIKE :title';
        $params[':title'] = '%' . trim($_GPC['keyword']) . '%';
    }


    if (!empty($_GPC['category']['thirdid'])) {
        $condition .= ' AND (g.`tcate` = :tcate or g.tcates = :tcate)';
        $params[':tcate'] = intval($_GPC['category']['thirdid']);
    }
    if (!empty($_GPC['category']['childid'])) {
        $condition .= ' AND (g.`ccate` = :ccate or g.ccates = :ccate)';
        $params[':ccate'] = intval($_GPC['category']['childid']);
    }
    if (!empty($_GPC['category']['parentid'])) {
        $condition .= ' AND (g.`pcate` = :pcate or g.pcates = :pcate)' ;
        $params[':pcate'] = intval($_GPC['category']['parentid']);
    }


    if (!empty($_GPC['category2']['thirdid'])) {
        $condition .= ' AND (g.`tcate1` = :tcate2 or g.tcates2 = :tcate2)';
        $params[':tcate2'] = intval($_GPC['category2']['thirdid']);
    }
    if (!empty($_GPC['category2']['childid'])) {
        $condition .= ' AND (g.`ccate1` = :ccate2 or g.ccates2 = :ccate2)';
        $params[':ccate2'] = intval($_GPC['category2']['childid']);
    }
    if (!empty($_GPC['category2']['parentid'])) {
        $condition .= ' AND (g.`pcate1` = :pcate2 or g.pcates2 = :pcate2)' ;
        $params[':pcate2'] = intval($_GPC['category2']['parentid']);
    }


    if ($_GPC["status"] != '') {
        $condition .= ' AND g.`status` = :status';
        $params[':status'] = intval($_GPC['status']);
    }

    //供应商搜索
    if (!empty($_GPC["supplier_uid"]) && $_GPC["supplier_uid"] != 9999) {
        $condition .= " AND g.`supplier_uid` = "."$_GPC[supplier_uid]";
    }

	if ($_GPC["supplier_uid"] == 9999) {
        $condition .= ' AND g.`supplier_uid` = 0';
    }

    if(p('supplier')){
        $suproleid = pdo_fetchcolumn('select id from' . tablename('sz_yi_perm_role') . ' where status1 = 1');
        $userroleid = pdo_fetchcolumn('select roleid from ' . tablename('sz_yi_perm_user') . ' where uid=:uid and uniacid=:uniacid',array(':uid' => $_W['uid'],':uniacid' => $_W['uniacid']));

        //Author:RainYang Date:2016-04-09 Content:修改供应商判断条件,有可能上面两个id都是空的情况,照成商品不显示
        if((!empty($userroleid)) && ($userroleid == $suproleid)){
            $sql = 'SELECT g.*,cs.stock_total,cs.optionid,cs.id as stockid FROM ' . tablename('sz_yi_channel_stock') . ' cs left join ' . tablename('sz_yi_goods') . ' g on g.id = cs.goodsid' . $condition . ' and g.supplier_uid='.$_W['uid'].' ORDER BY g.`status` DESC, g.`displayorder` DESC,
                    `id` DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
            $sqls = 'SELECT COUNT(1) FROM ' . tablename('sz_yi_channel_stock') . ' cs left join ' . tablename('sz_yi_goods') . ' g on g.id = cs.goodsid' . $condition . ' and g.supplier_uid='.$_W['uid'];
            $total = pdo_fetchcolumn($sqls, $params);
        }
        else{
            $sql = 'SELECT g.*,cs.stock_total,cs.optionid,cs.id as stockid FROM ' . tablename('sz_yi_channel_stock') . ' cs left join ' . tablename('sz_yi_goods') . ' g on g.id = cs.goodsid' . $condition . ' ORDER BY  cs.`id` DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
            $sqls = 'SELECT COUNT(*) FROM ' . tablename('sz_yi_channel_stock') . ' cs left join ' . tablename('sz_yi_goods') . ' g on g.id = cs.goodsid' . $condition;
            $total = pdo_fetchcolumn($sqls, $params);
        }
    }else{
        $sql = 'SELECT g.*,cs.stock_total,cs.optionid,cs.id as stockid FROM ' . tablename('sz_yi_channel_stock') . ' cs left join ' . tablename('sz_yi_goods') . ' g on g.id = cs.goodsid' . $condition . ' ORDER BY cs.`id` DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $sqls = 'SELECT COUNT(1) FROM ' . tablename('sz_yi_channel_stock') . ' cs left join ' . tablename('sz_yi_goods') . ' g on g.id = cs.goodsid' . $condition;
        $total = pdo_fetchcolumn($sqls, $params);
    }
    $list  = pdo_fetchall($sql, $params);
    foreach ($list as &$row) {
        $row['stock_sum'] = pdo_fetchcolumn('SELECT sum(every_turn) FROM ' . tablename('sz_yi_channel_stock_log') . ' WHERE uniacid=:uniacid AND goodsid = :goodsid AND type = 1 AND openid=:openid',array(':uniacid' => $_W['uniacid'],':goodsid' => $row['id'], ':openid' => $openid));
        $row['stock_sold_sum'] = pdo_fetchcolumn('SELECT sum(every_turn) FROM ' . tablename('sz_yi_channel_stock_log') . ' WHERE uniacid=:uniacid AND goodsid = :goodsid AND type = 2 AND type = 3',array(':uniacid' => $_W['uniacid'],':goodsid' => $row['id']));
    }
    $pager = pagination($total, $pindex, $psize);
    
} elseif ($operation == 'delete') {
    ca('shop.goods.delete');
    $id  = intval($_GPC['id']);
    $row = pdo_fetch("SELECT id FROM " . tablename('sz_yi_channel_stock') . " WHERE id = :id AND uniacid=:uniacid", array(
        ':id' => $id,
        'uniacid' => $_W['uniacid']
    ));
    if (empty($row)) {
        message('抱歉，商品库存不存在或是已经被删除！');
    }
    pdo_delete('sz_yi_channel_stock', array('id' => $id, 'uniacid' => $_W['uniacid']));
    //安装芸众差价，删除房间类型商品同时删除房型表中的商品
    if(p('hotel')){
        $rooms = pdo_fetch("select * from " . tablename('sz_yi_hotel_room') . " where goodsid=:goodsid and  uniacid=:uniacid", array(
                      ":goodsid" => $id,":uniacid" => $_W['uniacid']
        ));
        if(!empty($rooms)){    
            pdo_query("delete from " . tablename('sz_yi_hotel_room') . " where id={$rooms['id']}");
        }
    }
    message('删除成功！', referer(), 'success');
} elseif ($operation == 'change') {
    $stockid = $_GPC['stockid'];
    $value = $_GPC['value'];
    $stock_before = pdo_fetch('SELECT * FROM ' . tablename('sz_yi_channel_stock') . ' WHERE id = :id', array(':id' => $stockid));
    pdo_update('sz_yi_channel_stock', array(
        'stock_total' => $value
    ), array(
        'id' => $stockid
    ));
    if ($value >= $stock_before['stock_total']) {
        $every_turn = $value - $stock_before['stock_total'];
        $type = 5;//加库存
    } elseif ($value <= $stock_before['stock_total']) {
        $every_turn = $stock_before['stock_total'] - $value;
        $type = 6;//减库存
    }
    $data = array(
        'uniacid' => $_W['uniacid'],
        'openid'  => $stock_before['openid'],
        'goodsid' => $stock_before['goodsid'],
        'every_turn' => $every_turn,
        'every_turn_price' => 0,
        'every_turn_discount' => 0,
        'goods_price' => 0,
        'paytime' => time(),
        'optionid' => $stock_before['optionid'],
        'type' => $type,
        'order_goodsid' => 0,
        'surplus_stock' => $value,
        'mid' => 0
        );
    pdo_insert('sz_yi_channel_stock_log',$data);
} elseif ($operation == 'detail') {
    $stockid = $_GPC['id']; 
    $stock = pdo_fetch('SELECT * FROM ' . tablename('sz_yi_channel_stock') . ' WHERE id = :id AND uniacid = :uniacid', array(':id' => $stockid, ':uniacid' => $_W['uniacid']));
    $list = $this->model->getMyOptionStockLog($stock['openid'],$stock['goodsid'],$stock['optionid']);
    //print_r($list);exit;
}
load()->func('tpl');
include $this->template('inventory');
