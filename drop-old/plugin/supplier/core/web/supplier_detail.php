<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;

$id = intval($_GPC['id']);
$supplier = pdo_fetch("select * from " . tablename('sz_yi_af_supplier') . " where uniacid={$_W['uniacid']} and id={$id}");
$avatar = pdo_fetchcolumn("select avatar from " . tablename('sz_yi_member') . " where uniacid={$_W['uniacid']} and openid='{$supplier['openid']}'");
//自定义表单数据
$diyform_flag   = 0;
$diyform_plugin = p('diyform');
if ($diyform_plugin) {
    if (!empty($supplier['diymemberdata'])) {
        $diyform_flag = 1;
        $fields       = iunserializer($supplier['diymemberfields']);
    }
}
load()->func('tpl');
include $this->template('supplier_detail');

