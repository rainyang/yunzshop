<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
session_start();
global $_W, $_GPC;
$openid = m('user')->getOpenid();
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') {
    $pcate = intval($_GPC['pcate']);
    $category  = set_medias(pdo_fetchall(" SELECT id,name,thumb FROM " .tablename('sz_yi_store_category'). " WHERE enabled=1 and parentid=:id and uniacid=:uniacid  ORDER BY displayorder DESC ", array(':id' => $pcate, ':uniacid' => $_W['uniacid'])), 'thumb');
} elseif ($operation == 'getstore' && $_W['isajax']) {
    $pcate = intval($_GPC['pcate']);
    $page = intval($_GPC['page']);
    $ccate = intval($_GPC['ccate']);
    $params             = array();
    $params[':uniacid'] = $_W['uniacid'];
    $condition          = " and a.uniacid=:uniacid and a.status=1";
    if (!empty($pcate)) {
        $condition .= " AND a.pcate = :pcate";
        $params[':pcate'] = $pcate;
    }
    if (!empty($ccate)) {
        $condition .= " AND a.ccate = :ccate";
        $params[':ccate'] = $ccate;
    }
    if (!empty($_SESSION['city'])) {
        $condition .= " AND a.city like :city";
        $params[':city'] = $_SESSION['city'];
    }
    if (!empty($_GPC['street'])) {
        $condition .= " AND a.street like :street";
        $params[':street'] = $_GPC['street'];
    }
    if (!empty($_GPC['displayorder']) && $_GPC['displayorder'] == 'low') {
        $condition .= " ORDER by singleprice ASC";
    } elseif (!empty($_GPC['displayorder']) && $_GPC['displayorder'] == 'high') {
        $condition .= " ORDER by singleprice DESC";
    }

    $goods_list = set_medias(pdo_fetchall("SELECT a.thumb,a.storename,a.id,a.lng,a.lat,a.street,a.address,a.singleprice,b.name FROM " .tablename('sz_yi_store'). " a LEFT JOIN ".tablename('sz_yi_store_category'). " b ON b.id=a.ccate and b.uniacid=a.uniacid WHERE 1 {$condition}  ", $params), 'thumb');
    //门店评论平均数
    $store_comment = pdo_fetchall(" SELECT storeid,avg(level) as level FROM " .tablename('sz_yi_order_comment'). " WHERE uniacid=:uniacid GROUP BY storeid", array( ':uniacid' => $_W['uniacid']));
    $store_level = array();
    foreach ($store_comment as $value) {
        $store_level[$value['storeid']] = $value['level'];
    }
    //按照距离排序
    $distance = array();
    foreach ($goods_list as $key => &$row) {

        if (count($store_level) > 0) {
            $goods_list[$key]['level'] = $store_level[$row['id']];
        }
        $goods_list[$key]['distance'] = getDistance($row['lat'], $row['lng'], $_SESSION['lat'], $_SESSION['lng']);
        if ($goods_list[$key]['distance'] >= 1000) {
            $goods_list[$key]['distance'] = round($goods_list[$key]['distance']/1000,1);
            $goods_list[$key]['km'] = 1;
        }
        $goods_list[$key]['address'] = $row['street'];
        $distance[] =  $goods_list[$key]['distance'];
        if (empty($row['lng']) || empty($row['lat']) ) {
            unset($goods_list[$key]);
        }
    }

    if (empty($_GPC['displayorder']) || $_GPC['displayorder'] == 'display' || $_GPC['displayorder'] == 'near_me') {
        array_multisort($distance, SORT_ASC, $goods_list);
    }
    //分页
    $size = $page*10;
    $_size = ($page-1)*10;
    foreach ($goods_list as $k => $value) {
        if ($k < $_size || $k >= $size) {
            unset($goods_list[$k]);
        }
    }
    return show_json(1, array('goods' => $goods_list, 'pagesize' => 10));
}

include $this->template('store_list');


