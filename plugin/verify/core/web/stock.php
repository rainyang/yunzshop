<?php

global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') {
    $id = intval($_GPC['id']);
    $pindex = max(1, intval($_GPC['page']));
    $psize = 10;
    $store = pdo_fetchall(" SELECT goodsid,optionid,storeid,total FROM " .tablename('sz_yi_store_goods'). " WHERE storeid=:storeid and uniacid=:uniacid LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(':storeid' => $id, ':uniacid' => $_W['uniacid']));
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " .tablename('sz_yi_store_goods'). " WHERE storeid=:storeid and uniacid=:uniacid ", array(':storeid' => $id, ':uniacid' => $_W['uniacid']));
    //echo '<pre>'; print_r($store);exit;
    foreach ($store as $key => $row) {
        $store[$key]['title'] = pdo_fetchcolumn(" SELECT title FROM " .tablename('sz_yi_goods'). " WHERE id=".$row['goodsid']);
        $store[$key]['thumb'] = set_medias(pdo_fetchcolumn(" SELECT thumb FROM " .tablename('sz_yi_goods'). " WHERE id=".$row['goodsid']));
        if (!empty($row['optionid'])) {
            $store[$key]['option_title'] = pdo_fetchcolumn(" SELECT title FROM " .tablename('sz_yi_goods_option'). " WHERE id=".$row['optionid']);
        }

    }
    $pager = pagination($total, $pindex, $psize);

    if (checksubmit("submit")) {
        $total = $_GPC['total'];
        $storeid = intval($_GPC['storeid']);
        foreach ($total as $key => $value) {

            foreach ($value as $k => $val) {
                if ($k != '0') {
                    pdo_update('sz_yi_store_goods', array('total' => $val), array('optionid' => $k, 'goodsid' => $key, 'storeid' => storeid));
                } else {
                    pdo_update('sz_yi_store_goods', array('total' => $val), array( 'goodsid' => $key, 'optionid' => 0, 'storeid' => $storeid));
                }
            }
        }
        message('修改成功！', $this->createPluginWebUrl('verify/stock', array(
            'op' => 'display', 'id' => $id
        )), 'success');

    }

}  else if ($operation == 'delete') {
    $id = intval($_GPC['id']);
    $storeid = intval($_GPC['storeid']);
    pdo_delete('sz_yi_store_goods', array('storeid' => $storeid, 'goodsid' => $id));
    message('删除成功！', $this->createPluginWebUrl('verify/stock', array(
        'op' => 'display', 'id' => $storeid
    )), 'success');

}
include $this->template('stock');
