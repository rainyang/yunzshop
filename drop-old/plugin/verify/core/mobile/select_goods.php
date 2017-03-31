<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$operation  = !empty($_GPC['op']) ? $_GPC['op'] : 'moren';
$openid     = m('user')->getOpenid();
$uniacid    = $_W['uniacid'];
$specs = array();
if($_W['isajax']){
    $storeid = intval($_GPC['storeid']);
    $can_goods = pdo_fetchall(" SELECT * FROM " .tablename('sz_yi_goods'). " WHERE uniacid=:uniacid and status=1 and deleted=0 and isverify=2", array(':uniacid' => $_W['uniacid']));
    //遍历所有核销商品，取指定门店为空或者指定门店中有此门店的商品
    foreach ($can_goods as $row) {
        if (!empty($row['storeids'])) {
            $storeids = explode(',', $row['storeids']);
            foreach ($storeids as $r) {
                if ($storeid == $r) {
                    $goodsids[] = $row['id'];
                }
            }
        } else {
            $goodsids[] = $row['id'];
        }
    }
    $goodsid = implode(',', $goodsids);


    if ($operation == 'moren') {
            $args = array(
                'pcate' => $_GPC['pcate'],
                'ids' => $goodsid

            );
    } else if($operation == 'second') {
            $args=array(
                'ccate' => $_GPC['ccate'],
                'ids' => $goodsid
            );
    } else if ($operation == 'third') {
        $args = array(
            'tcate' => $_GPC['tcate'],
            'ids' => $goodsid
        );
    } else if ($operation == 'getdetail') {
        $args = array(
            'id' => intval($_GPC['id'])
        );
        $specs = pdo_fetchall("SELECT * FROM ".tablename('sz_yi_goods_spec')." WHERE goodsid=:id and uniacid=:uniacid",array(':id' => $args['id'], ':uniacid' => $_W['uniacid']));
        foreach ($specs as $key => $item) {
            $specs[$key]['sub'] = pdo_fetchall("SELECT * FROM ".tablename('sz_yi_goods_spec_item')." WHERE specid=:specid and uniacid=:uniacid",array(':specid' => $item['id'], ':uniacid' => $_W['uniacid']));
        }
        $good = set_medias(pdo_fetch("SELECT * FROM ".tablename('sz_yi_goods')." WHERE id=".$args['id']),'thumb');
    }




    $args['isverify'] = 2;


    $goods = m('goods')->getList($args);
    //替换门店商品库存（没有规格的商品）
    if ($page['isstore'] == 1) {
        foreach ($goods as $key => &$row) {
            $store_goods = pdo_fetch(" SELECT * FROM ".tablename('sz_yi_store_goods')." WHERE goodsid=:goodsid and uniacid=:uniacid and optionid=0 and storeid=:storeid", array(':goodsid' => $row['id'], ':uniacid' => $_W['uniacid'], ':storeid' => intval($page['storeid'])));
            if ($store_goods) {
                $goods[$key]['total'] = $store_goods['total'];
            }

        }
    }

    return show_json(1,array('goods' => $goods, 'specs' => $specs, 'good' => $good));
}