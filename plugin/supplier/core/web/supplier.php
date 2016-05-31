<?php
global $_W, $_GPC;
$operation   = empty($_GPC['op']) ? 'display' : $_GPC['op'];
if ($operation == 'display') {
    $roleid = pdo_fetchcolumn('select id from ' . tablename('sz_yi_perm_role') . ' where status1=1');
    $where = '';
    if(empty($_GPC['uid'])){
        $where .= ' and uniacid=' . $_W['uniacid'];
    }else{
        $where .= ' and uid="' . $_GPC['uid'] . '" and uniacid=' . $_W['uniacid'];
    }
    $list = pdo_fetchall('select * from ' . tablename('sz_yi_perm_user') . ' where roleid='. $roleid . " " .$where);
    $total = count($list);
} else if ($operation == 'detail') {
    $applyid = intval($_GPC['applyid']);
    $finish = $_GPC['type'];
    if (!empty($applyid)) {
        $apply_info = pdo_fetch("select * from " . tablename('sz_yi_supplier_apply') . " where uniacid={$_W['uniacid']} and id={$applyid}");
        $openid = pdo_fetchcolumn("select openid from " . tablename('sz_yi_perm_user') . " where uniacid={$_W['uniacid']} and uid='{$apply_info['uid']}'");
        if (empty($openid)) {
            $supplierinfo = pdo_fetch("select * from " . tablename('sz_yi_perm_user') . " where uniacid={$_W['uniacid']} and uid='{$apply_info['uid']}'");
        } else {
            $supplierinfo = pdo_fetch("select * from " . tablename('sz_yi_af_supplier') . " where uniacid={$_W['uniacid']} and openid='{$openid}'");
        }
        if(!empty($supplierinfo['openid'])){
            $saler = m('member')->getInfo($supplierinfo['openid']);
            $avatar = $saler['avatar'];
        }
        $diyform_flag   = 0;
        $diyform_plugin = p('diyform');
        if ($diyform_plugin) {
            if (!empty($supplierinfo['diymemberdata'])) {
                $diyform_flag = 1;
                $fields       = iunserializer($supplierinfo['diymemberfields']);
            }
        }
    } else {
        $uid = intval($_GPC['uid']);
        $openid = $_GPC['openid'];
        if (empty($openid)) {
            $supplierinfo = pdo_fetch("select * from " . tablename('sz_yi_perm_user') . " where uniacid={$_W['uniacid']} and uid='{$uid}'");
        } else {
            $supplierinfo = pdo_fetch("select * from " . tablename('sz_yi_af_supplier') . " where uniacid={$_W['uniacid']} and openid='{$openid}'");
            $uid = pdo_fetchcolumn("select uid from " . tablename('sz_yi_perm_user') . " where uniacid={$_W['uniacid']} and openid='{$openid}'");
        }
        if(!empty($supplierinfo['openid'])){
            $saler = m('member')->getInfo($supplierinfo['openid']);
        }
        $diyform_flag   = 0;
        $diyform_plugin = p('diyform');
        if ($diyform_plugin) {
            if (!empty($supplierinfo['diymemberdata'])) {
                $diyform_flag = 1;
                $fields       = iunserializer($supplierinfo['diymemberfields']);
            }
        }
        $totalmoney = pdo_fetchcolumn("select sum(apply_money) from " . tablename('sz_yi_supplier_apply') . " where uniacid={$_W['uniacid']} and uid={$uid}");
        $sp_goods = pdo_fetchall("select og.* from " . tablename('sz_yi_order_goods') . " og left join " .tablename('sz_yi_order') . " o on (o.id=og.orderid) where og.uniacid={$_W['uniacid']} and og.supplier_uid={$uid} and o.status=3 and og.supplier_apply_status=0");
        foreach ($sp_goods as $key => $value) {
            if ($value['goods_op_cost_price'] > 0) {
                $costmoney += $value['goods_op_cost_price'] * $value['total'];
            } else {
                $option = pdo_fetch("select * from " . tablename('sz_yi_goods_option') . " where uniacid={$_W['uniacid']} and goodsid={$value['goodsid']} and id={$value['optionid']}");
                if ($option['costprice'] > 0) {
                    $costmoney += $option['costprice'] * $value['total'];
                } else {
                    $goods_info = pdo_fetch("select * from" . tablename('sz_yi_goods') . " where uniacid={$_W['uniacid']} and id={$value['goodsid']}");
                    $costmoney += $goods_info['costprice'] * $value['total'];
                }
            }
        }
        if(checksubmit('submit')){
            $data = is_array($_GPC['data']) ? $_GPC['data'] : array();
            pdo_update('sz_yi_perm_user', $data, array(
                'openid' => $openid
            ));
            message('保存成功!', $this->createPluginWebUrl('supplier/supplier'), 'success');
        }
    }
} 
load()->func('tpl');
include $this->template('supplier');
