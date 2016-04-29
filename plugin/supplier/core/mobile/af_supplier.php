<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$openid = m('user')->getOpenid();
$member = m('member')->getInfo($openid);
$af_supplier = pdo_fetch("select * from " . tablename("sz_yi_af_supplier") . " where openid='{$openid}' and uniacid={$_W['uniacid']}");
$diyform_plugin = p("diyform");
$order_formInfo = false;
if ($diyform_plugin) {
    $diyform_set = $diyform_plugin->getSet();
    if (!empty($diyform_set["supplier_diyform_open"])) {
        $supplierdiyformid = intval($diyform_set["supplier_diyform"]);
        if (!empty($supplierdiyformid)) {
            $supplier_formInfo = $diyform_plugin->getDiyformInfo($supplierdiyformid);
            $fields         = $supplier_formInfo["fields"];
            $f_data         = $diyform_plugin->getLastOrderData($supplierdiyformid, $member);
        }
    }
}
if ($_W['isajax']) {
    if ($_W['ispost']) {
        $memberdata = $_GPC['memberdata'];
		$memberdata['openid'] = $openid;
		$memberdata['uniacid'] = $_W['uniacid'];
        pdo_insert('sz_yi_af_supplier',$memberdata);
        show_json(1);
    }
	show_json(1, array(
        'member' => $member
    ));
}
if(!isMobile()){
    include $this->template('member/center');
}
include $this->template('af_supplier');
