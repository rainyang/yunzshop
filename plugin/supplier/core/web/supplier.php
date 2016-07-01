<?php
global $_W, $_GPC;
$operation   = empty($_GPC['op']) ? 'display' : $_GPC['op'];
if ($operation == 'display') {
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 20;
    $roleid = pdo_fetchcolumn('select id from ' . tablename('sz_yi_perm_role') . ' where status1=1');
    $where = '';
    if(empty($_GPC['uid'])){
        $where .= ' and uniacid=' . $_W['uniacid'];
    }else{
        $where .= ' and uid="' . $_GPC['uid'] . '" and uniacid=' . $_W['uniacid'];
    }
    if (p('merchant') && !empty($_GPC['member_id'])) {
        $ismerchant = true;
        $member_id = intval($_GPC['member_id']);
        $supplier_uids = pdo_fetchall("select distinct supplier_uid from " . tablename('sz_yi_merchants') . " where uniacid={$_W['uniacid']} and member_id={$member_id}");
        $uids = "";
        foreach ($supplier_uids as $key => $value) {
            if ($key == 0) {
                $uids .= $value['supplier_uid'];
            } else {
                $uids .= ','.$value['supplier_uid'];
            }
        }
        if (empty($uids)) {
            $uids = 0;
        }
        $list = pdo_fetchall('select * from ' . tablename('sz_yi_perm_user') . ' where roleid='. $roleid . " and uniacid={$_W['uniacid']} and uid in ({$uids}) LIMIT " . ($pindex - 1) * $psize . "," . $psize);
    } else {
        $ismerchant = false;
        $list = pdo_fetchall('select * from ' . tablename('sz_yi_perm_user') . ' where roleid='. $roleid . " " .$where." LIMIT " . ($pindex - 1) * $psize . "," . $psize);
    }
    $total = count($list);
    $pager = pagination($total, $pindex, $psize);
} else if ($operation == 'detail') {
    $applyid = intval($_GPC['applyid']);
    $finish = $_GPC['type'];
    if (!empty($applyid)) {
        $apply_info = pdo_fetch("select * from " . tablename('sz_yi_supplier_apply') . " where uniacid={$_W['uniacid']} and id={$applyid}");
        $openid = pdo_fetchcolumn("select openid from " . tablename('sz_yi_perm_user') . " where uniacid={$_W['uniacid']} and uid='{$apply_info['uid']}'");
        if (!empty($openid)) {
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
            if (empty($supplierinfo)) {
                $supplierinfo = pdo_fetch("select * from " . tablename('sz_yi_perm_user') . " where uniacid={$_W['uniacid']} and openid='{$openid}'");
                $uid = pdo_fetchcolumn("select uid from " . tablename('sz_yi_perm_user') . " where uniacid={$_W['uniacid']} and openid='{$openid}'");
            }
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
            if (!empty($data['openid'])) {
                $result = pdo_fetch("select * from " . tablename('sz_yi_perm_user') . " where uniacid={$_W['uniacid']} and openid='{$data['openid']}'");
                if (!empty($result)) {
                    if ($data['openid'] != $supplierinfo['openid']) {
                        message('该微信已绑定，请更换!', $this->createPluginWebUrl('supplier/supplier'), 'error');
                    } else {
                        pdo_update('sz_yi_perm_user', $data, array(
                            'openid' => $openid
                        ));
                        message('保存成功!', $this->createPluginWebUrl('supplier/supplier'), 'success');
                    }
                } else {
                    pdo_update('sz_yi_perm_user', $data, array(
                        'openid' => $openid
                    ));
                    message('保存成功!', $this->createPluginWebUrl('supplier/supplier'), 'success');
                }
            } else {
                pdo_update('sz_yi_perm_user', $data, array(
                        'uid' => $uid
                    ));
                message('保存成功!', $this->createPluginWebUrl('supplier/supplier'), 'success');
            }
        }
    }
} else if ($operation == 'merchant') {
    $uid = intval($_GPC['uid']);
    $merchants = pdo_fetchall("select * from " . tablename('sz_yi_merchants') . " where uniacid={$_W['uniacid']} and supplier_uid={$uid}");
    $total = count($merchants);
    foreach ($merchants as &$value) {
        $merchants_member = m('member')->getMember($value['openid']);
        $value['avatar'] = $merchants_member['avatar'];
        $value['nickname'] = $merchants_member['nickname'];
        $value['realname'] = $merchants_member['realname'];
        $value['mobile'] = $merchants_member['mobile'];
    }
    unset($value);
} else if ($operation == 'merchant_post') {
    $uid = intval($_GPC['uid']);
    if(checksubmit('submit')){
        $data = is_array($_GPC['data']) ? $_GPC['data'] : array();
        if (empty($data['openid'])) {
            message('请选择微信!', referer(), 'error');
        } else {
            $result = pdo_fetch("select * from " . tablename('sz_yi_merchants') . " where uniacid={$_W['uniacid']} and openid='{$data['openid']}' and supplier_uid={$uid}");
            if (!empty($result)) {
                message('该微信角色已经是此供应商的招商员!', referer(), 'error');
            } else {
                $data['member_id'] = pdo_fetchcolumn("select id from " . tablename('sz_yi_member') . " where uniacid={$_W['uniacid']} and openid='{$data['openid']}'");
            }
        }
        if (empty($data['commissions'])) {
            message('请输入佣金比例!', referer(), 'error');
        }
        $data['uniacid'] = $_W['uniacid'];
        $data['supplier_uid'] = $uid;
        pdo_insert('sz_yi_merchants',$data);
        message('添加招商员成功!', $this->createPluginWebUrl('supplier/supplier', array('op' => 'merchant', 'uid' => $uid)), 'success');
    }
} else if ($operation == 'merchant_delete') {
    $id = intval($_GPC['id']);
    if (empty($id)) {
        message('未找到该招商员!', referer(), 'error');
    }
    pdo_delete('sz_yi_merchants', array('id' => $id, 'uniacid' => $_W['uniacid']));
    message('删除成功!', referer(), 'success');
}
load()->func('tpl');
include $this->template('supplier');
