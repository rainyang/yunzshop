<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
session_start();
global $_W, $_GPC;
$openid = m('user')->getOpenid();
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$verifyset  = m('common')->getSetData();
$allset = iunserializer($verifyset['plugins']);
$store_total = false;
if (isset($allset['verify']) && $allset['verify']['store_total'] == 1) {
    $store_total = true;
}
if ($operation == 'display' && $_W['isajax']) {
    $id = intval($_GPC['id']);
    if (empty($id)) {
        return show_json(0);
    }
    $store_detail = set_medias(pdo_fetch(" SELECT info,thumb,id,storename,singleprice,province,city,area,street,address,tel,lat,lng,cashierid FROM " .tablename('sz_yi_store'). " WHERE id=:id and uniacid=:uniacid ", array(':id' => $id, ':uniacid' => $_W['uniacid'])), 'thumb');
    $store_comment = pdo_fetchall(" SELECT level FROM " .tablename('sz_yi_order_comment'). " WHERE storeid=:storeid and uniacid=:uniacid ", array(':storeid' => $id, ':uniacid' => $_W['uniacid']));
    $count = count($store_comment);
    $l = 0;
    foreach ($store_comment as $v) {
        $l += $v['level'];
    }
    $level = $l/$count;
    $store_detail['level'] = $level;
    if ($store_total) {
        $store_goods = set_medias(pdo_fetchall(" SELECT b.id,b.title,b.marketprice,b.productprice,b.thumb,b.sales FROM " .tablename('sz_yi_goods'). " b LEFT JOIN " .tablename('sz_yi_store_goods'). " a on b.id=a.goodsid and b.uniacid=a.uniacid WHERE b.isverify=2 and a.storeid=:id and a.uniacid=:uniacid GROUP BY a.goodsid ORDER BY b.sales DESC LIMIT 3 ", array(':id' => $id, ':uniacid' => $_W['uniacid'])), 'thumb');
    } else {
        $store_goods = set_medias(pdo_fetchall(" SELECT id,title,marketprice,productprice,thumb,sales FROM " .tablename('sz_yi_goods'). " WHERE :id in (storeids) and uniacid=:uniacid and deleted=0 and status=1 and isverify=2 union all SELECT id,title,marketprice,productprice,thumb,sales FROM " .tablename('sz_yi_goods'). " WHERE storeids='' and uniacid=:uniacid and deleted=0 and status=1 and isverify=2 ORDER BY sales DESC LIMIT 3 ", array(':id' => $id, ':uniacid' => $_W['uniacid'])), 'thumb');
    }




    if ($store_total) {
        $goods_list = set_medias(pdo_fetchall("SELECT a.storename,c.id,a.lng,a.lat,a.area,a.address,b.goodsid,c.title,c.sales,c.marketprice,c.productprice,c.thumb FROM " .tablename('sz_yi_store'). " a LEFT JOIN " .tablename('sz_yi_store_goods'). " b ON b.storeid=a.id and b.uniacid=a.uniacid"." LEFT JOIN ".tablename('sz_yi_goods'). " c on c.id=b.goodsid and c.uniacid=a.uniacid and c.isverify=2  WHERE a.status=1 and a.uniacid=:uniacid and a.city like :city GROUP BY b.goodsid limit 5", array(':uniacid' => $_W['uniacid'], ':city' => trim($_SESSION['city']))), 'thumb');
    } else {
        $goods_list = set_medias(pdo_fetchall("SELECT a.storename,c.id,a.lng,a.lat,a.area,a.address,c.title,c.sales,c.marketprice,c.productprice,c.thumb FROM " .tablename('sz_yi_goods'). " c right JOIN " .tablename('sz_yi_store')." a on find_in_set(a.id,c.storeids) and a.uniacid=c.uniacid and a.status=1 and a.city like :city WHERE c.isverify=2 and c.uniacid=:uniacid and c.deleted=0 and c.status=1 union all SELECT a.storename,c.id,a.lng,a.lat,a.area,a.address,c.title,c.sales,c.marketprice,c.productprice,c.thumb FROM ".tablename('sz_yi_goods')." c left JOIN " .tablename('sz_yi_store'). " a on a.uniacid=c.uniacid and a.status=1 and a.city like :city WHERE c.isverify=2 and c.uniacid=:uniacid and c.deleted=0 and c.storeids = '' and c.status=1 limit 5", array(':uniacid' => $_W['uniacid'], ':city' => trim($_SESSION['city']))), 'thumb');
    }



    //查出数据按距离排序
    $distance = array();
    foreach ($goods_list as $key => &$row) {

        $goods_list[$key]['distance'] = getDistance($row['lat'], $row['lng'], $_SESSION['lat'], $_SESSION['lng']);
        if ($goods_list[$key]['distance'] >= 1000) {
            $goods_list[$key]['distance'] = round($goods_list[$key]['distance']/1000,1);
            $goods_list[$key]['km'] = 1;
            $distance[] =  $goods_list[$key]['distance']*1000;
        } else {
            $distance[] =  $goods_list[$key]['distance'];
        }

        $goods_list[$key]['address'] = $row['area'].$row['address'];
        if (empty($row['lng']) || empty($row['lat']) || empty($row['id'])) {
            unset($goods_list[$key]);
        }

    }
    array_multisort($distance, SORT_ASC, $goods_list);

    $store_list = set_medias(pdo_fetchall(" SELECT a.thumb,a.storename,a.id,a.lng,a.lat,a.street,a.address,a.singleprice,b.name FROM " .tablename('sz_yi_store'). " a LEFT JOIN ".tablename('sz_yi_store_category'). " b ON b.id=a.ccate and b.uniacid=a.uniacid WHERE a.uniacid=:uniacid and a.city like :city limit 5", array(':uniacid' => $_W['uniacid'], ':city' => $_SESSION['city'])), 'thumb');
    $distance_s = array();
    foreach ($store_list as $key => &$row1) {

        $store_list[$key]['distance'] = getDistance($row1['lat'], $row1['lng'], $_SESSION['lat'], $_SESSION['lng']);
        if ($store_list[$key]['distance'] >= 1000) {
            $store_list[$key]['distance'] = round($store_list[$key]['distance']/1000,1);
            $store_list[$key]['km'] = 1;
            $distance_s[] =  $store_list[$key]['distance']*1000;
        } else {
            $distance_s[] =  $store_list[$key]['distance'];
        }

        if (empty($row1['lng']) || empty($row1['lat']) || $row1['id'] == $id) {
            unset($store_list[$key]);
        }

    }
    array_multisort($distance_s, SORT_ASC, $store_list);
    return show_json(1,array('store' => $store_detail, 'store_goods' => $store_goods, 'near_goods' => $goods_list, 'store_list' => $store_list));
}

include $this->template('store_detail');