<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
session_start();
global $_W, $_GPC;
$verifyset  = m('common')->getSetData();
$allset = iunserializer($verifyset['plugins']);
$store_total = false;
if (isset($allset['verify']) && $allset['verify']['store_total'] == 1) {
    $store_total = true;
}
$openid = m('user')->getOpenid();
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($_W['isajax'] || $_W['ispost']) {
    if ($operation == 'display') {
        $page = $_GPC['page'];
        $category = array();
        $total = count(pdo_fetchall(" SELECT id,name,thumb FROM " .tablename('sz_yi_store_category'). " WHERE enabled=1 and ishome= 1 and uniacid=:uniacid and parentid=0 ", array(':uniacid' => $_W['uniacid'])));
        $page_total = is_float($total/10)  ? $total/10+1 : $total/10;
        //查询所有分类并以十个分类为一个单位存入数组 （LBS首页分类滑动）
        for ($i=$page;$i<=$page_total;$i++) {
            $category[$i] = set_medias(pdo_fetchall(" SELECT id,name,thumb FROM " .tablename('sz_yi_store_category'). " WHERE enabled=1 and ishome= 1 and uniacid=:uniacid and parentid=0 order by displayorder limit " . ($i - 1) * 10 . ',' . 10, array(':uniacid' => $_W['uniacid'])), 'thumb');
        }

        $set = $this->getSet();
        $set = set_medias($set, array('advtitle1','advtitle2','advtitle3','advtitle4'));
        show_json(1, array('category' => $category, 'set' => $set));
    } elseif ($operation == 'goods') {
        $page = $_GPC['page'];
        if (!empty($_GPC['lng'])) {
            $_SESSION['lng'] = $_GPC['lng'];
        }
        if (!empty($_GPC['lat'])) {
            $_SESSION['lat'] = $_GPC['lat'];
        }
        if (!empty($_GPC['city'])) {
            $_SESSION['city'] = $_GPC['city'];
        }
        if (!empty($_GPC['province'])) {
            $_SESSION['province'] = $_GPC['province'];
        }
        if (!empty($_GPC['area'])) {
            $_SESSION['area'] = $_GPC['area'];
        }
        if (!empty($_GPC['street'])) {
            $_SESSION['street'] = $_GPC['street'];
        }
        if ($store_total) {
            $goods_list = set_medias(pdo_fetchall("SELECT a.storename,c.id,a.lng,a.lat,a.area,a.address,b.goodsid,c.title,c.sales,c.marketprice,c.productprice,c.thumb FROM " .tablename('sz_yi_store'). " a LEFT JOIN " .tablename('sz_yi_store_goods'). " b ON b.storeid=a.id and b.uniacid=a.uniacid"." LEFT JOIN ".tablename('sz_yi_goods'). " c on c.id=b.goodsid and c.uniacid=a.uniacid and c.status=1 and c.isverify=2 WHERE a.status=1 and a.uniacid=:uniacid and a.city like :city GROUP BY b.goodsid  ", array(':uniacid' => $_W['uniacid'], ':city' => trim($_SESSION['city']))), 'thumb');
        } else {
            $goods_list = set_medias(pdo_fetchall("SELECT a.storename,c.id,a.lng,a.lat,a.area,a.address,c.title,c.sales,c.marketprice,c.productprice,c.thumb FROM " .tablename('sz_yi_goods'). " c right JOIN " .tablename('sz_yi_store')." a on find_in_set(a.id,c.storeids) and a.uniacid=c.uniacid and a.status=1 and a.city like :city WHERE c.isverify=2 and c.uniacid=:uniacid and c.deleted=0 and c.status=1 union all SELECT a.storename,c.id,a.lng,a.lat,a.area,a.address,c.title,c.sales,c.marketprice,c.productprice,c.thumb FROM ".tablename('sz_yi_goods')." c left JOIN " .tablename('sz_yi_store'). " a on a.uniacid=c.uniacid and a.status=1 and a.city like :city WHERE c.isverify=2 and c.uniacid=:uniacid and c.deleted=0 and c.storeids = '' and c.status=1 ", array(':uniacid' => $_W['uniacid'], ':city' => trim($_SESSION['city']))), 'thumb');
        }

        //给数据按距离排序
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

        $size = $page*10;
        $_size = ($page-1)*10;
        foreach ($goods_list as $k => $value) {
            if ($k < $_size || $k >= $size) {
                unset($goods_list[$k]);
            }
        }

        show_json(1, array('goods' => $goods_list, 'pagesize' => 10));

    }

}
if ($operation == 'selectaddress') {
    if (!empty($_GPC['city'])) {
        $_SESSION['city'] = $_GPC['city'];
    }
    if (!empty($_GPC['province'])) {
        $_SESSION['province'] = $_GPC['province'];
    }
}
if ($operation == 'location') {
    include $this->template('address');
} else {
    include $this->template('store_index');
}

