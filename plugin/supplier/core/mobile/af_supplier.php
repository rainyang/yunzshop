<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$openid = m('user')->getOpenid();
$af_supplier = pdo_fetch("select * from " . tablename("sz_yi_af_supplier") . " where openid='{$openid}' and uniacid={$_W['uniacid']}");
$supplier_set = p('supplier')->getSet();
$switch = false;
if ($supplier_set['switch'] == 1) {
    $switch = true;
}
$template_flag  = 0;
$diyform_plugin = p('diyform');
if ($diyform_plugin) {
    $set_config        = $diyform_plugin->getSet();
    $supplier_diyform_open = $set_config['supplier_diyform_open'];
    if ($supplier_diyform_open == 1) {
        $template_flag = 1;
        $diyform_id    = $set_config['supplier_diyform'];
        if (!empty($diyform_id)) {
            $formInfo     = $diyform_plugin->getDiyformInfo($diyform_id);
            $fields       = $formInfo['fields'];
            $diyform_data = iunserializer($af_supplier['diymemberdata']);
            $f_data       = $diyform_plugin->getDiyformData($diyform_data, $fields, $af_supplier);
        }
    }
}
if ($_W['isajax']) {
    if ($_W['ispost']) {
        if ($template_flag == 1) {
            $memberdata = $_GPC['memberdata'];
            $data                      = array();
            $m_data                    = array();
            $mc_data                   = array();
            $insert_data               = $diyform_plugin->getInsertData($fields, $memberdata);
            $data                      = $insert_data['data'];
            $m_data                    = $insert_data['m_data'];
            $mc_data                   = $insert_data['mc_data'];
            $m_data['diymemberid']     = $diyform_id;
            $m_data['diymemberfields'] = iserializer($fields);
            $m_data['diymemberdata']   = $data;
            $m_data['openid'] = $openid;
            $m_data['uniacid'] = $_W['uniacid'];
            $result = pdo_fetch('select * from ' . tablename('users') . " where username='".$m_data['username']."'");
            if (!empty($result)) {
                return show_json(2);
            }
            pdo_insert('sz_yi_af_supplier',$m_data);
            if (!empty($af_supplier['uid'])) {
                load()->model('mc');
                if (!empty($mc_data)) {
                    mc_update($af_supplier['uid'], $mc_data);
                }
            }
        } else {
            $memberdata = array(
            'realname'      => $_GPC['memberdata']['realname'],
            'mobile'        => $_GPC['memberdata']['mobile'],
            'weixin'        => $_GPC['memberdata']['weixin'],
            'productname'   => $_GPC['memberdata']['productname'],
            'username'      => $_GPC['memberdata']['username'],
            'password'      => $_GPC['memberdata']['password'],
            'openid'        => $openid,
            'uniacid'       => $_W['uniacid']
            );
            $result = pdo_fetch('select * from ' . tablename('users') . " where username='".$memberdata['username']."'");
            if (!empty($result)) {
                return show_json(2);
            } 
            pdo_insert('sz_yi_af_supplier',$memberdata);
            if (!empty($af_supplier['uid'])) {
                $mcdata = $_GPC['mcdata'];
                load()->model('mc');
                mc_update($af_supplier['uid'], $mcdata);
            }
        }
        return show_json(1);
    }

    if (!empty($af_supplier)) {
        $is_supplier = true;
    } else {
        $is_supplier = false;
    }
    return show_json(1, array(
        'member' => $af_supplier,
        'is_supplier' => $is_supplier
    ));
}
if ($template_flag == 1) {
    include $this->template('diyform/af_supplier');
} else {
    include $this->template('af_supplier');
}
