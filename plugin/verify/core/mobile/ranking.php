<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}

global $_W, $_GPC;

$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$openid    = m('user')->getOpenid();
$member = m('member')->getMember($openid);
$uniacid   = $_W['uniacid'];





$default_avatar = "../addons/sz_yi/template/mobile/default/static/images/photo-mr.jpg";
if ($_W['isajax']) {
    if ($operation == 'display') {

        $pindex    = max(1, intval($_GPC['page']));
        $psize     = 10;
        $stores = pdo_fetchall("SELECT id,storename,member_id FROM ".tablename('sz_yi_store')." WHERE uniacid=:uniacid and status = 1", array(':uniacid' => $_W['uniacid']));
        foreach ($stores as $key => $val) {
            $totalprice = pdo_fetchall(" SELECT price FROM " .tablename('sz_yi_order'). " WHERE storeid=:id and uniacid=:uniacid and status = 3 ", array(':uniacid' => $_W['uniacid'], ':id' => $val['id']));
            foreach ($totalprice as $value) {
                $price += $value['price'];

            }
            $stores[$key]['price'] = $price;
            unset($stores[$key]['id']);

        }
        $condition = array();
        foreach ($stores as $v) {
            $condition[] = $v['price'];
        }
        array_multisort($condition, SORT_DESC, $stores);

        $total     = pdo_fetchcolumn("select count(*) from " . tablename('sz_yi_store') . " where status =1 and uniacid = ".$_W['uniacid']);
        foreach ($stores as $k => &$row) {
            $row['number'] = ($k+1) + ($pindex - 1) * $psize;
            $row['avatar'] = !empty($row['avatar'])?$row['avatar']:$default_avatar;
        }
        unset($row);
        return show_json(1, array(
            'total' => $total,
            'list' => $stores,
            'pagesize' => $psize
        ));



    }
}
include $this->template('ranking');
