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
    if (p('channel')) {
        $member = m('member')->getInfo($openid);
        $level  = pdo_fetch("SELECT * FROM " . tablename('sz_yi_channel_level') . " WHERE uniacid={$_W['uniacid']} AND id={$member['channel_level']}");
        $ischannelpick = $_GPC['ischannelpick'];
    }
    $pageid = intval($_GPC['pageid']);
    $page   = pdo_fetch('select * from '.tablename('sz_yi_chooseagent'). ' where id=:id and uniacid=:uniacid',array(':uniacid'=>$_W['uniacid'],':id'=>$pageid));
    if ($page['isstore'] == 1) {
        $goodsids = pdo_fetchall("SELECT distinct goodsid FROM ".tablename('sz_yi_store_goods')." WHERE storeid=:storeid and uniacid=:uniacid ", array(':uniacid' => $_W['uniacid'], ':storeid' => $page['storeid']));
    }


    $goodsid = array();

    foreach ($goodsids as $row) {

        $goodsid[] = $row['goodsid'];
    }
    $goodsid = implode(',', $goodsid);
    if (!empty($page['isopenchannel'])) {
        $args = array(
            'isopenchannel' => $page['isopenchannel']
        );
        if (!empty($ischannelpick)) {
            $args = array(
                'isopenchannel' => $page['isopenchannel'],
                'ischannelpick' => $ischannelpick,
                'openid'        => $openid
            );
        }
    } else {
        if($page['isopen']!=0){
            $args=array(
                'pcate'         => $_GPC['pcate'],
                'ccate'         => $_GPC['ccate'],
                'tcate'         => $_GPC['tcate'],
                'supplier_uid'  => $page['uid']
            );
        }else{
            if ($operation == 'moren') {
                $args = array(
                    'pcate' => $_GPC['pcate'],
                    'ids' => $goodsid

                );
            } else if($operation == 'second') {
                $args=array(
                    'pcate' => $page['pcate'],
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
        }
    }
    if (p('verify')) {
        if (empty($page['isstore']) && empty($_GPC['storeid'])) {
            $args['isverify'] = 1;
        } elseif (!empty($page['isstore']) || !empty($_GPC['storeid'])) {
            $args['isverify'] = 2;
        }
    }
    $args['choose'] = true;
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
    if (p('channel')) {
        foreach ($goods as $key => &$value) {
            if (empty($ischannelpick)) {
                $value['channel_price'] = number_format($value['marketprice'] * $level['purchase_discount']/100, 2);
                $value['channel_price'] = "/采购价：" . $value['channel_price'] . "元";
            } else {
                $value['channel_price'] = "";
            }
        }
        unset($value);
    }
    return show_json(1,array('goods' => $goods, 'specs' => $specs, 'good' => $good));
}