<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$openid = m('user')->getOpenid();
$af_supplier = pdo_fetch("select * from " . tablename("sz_yi_member") . " where openid='{$openid}' and uniacid={$_W['uniacid']} and isagency = 1");
$plc_bonus = p('bonus');
$level = $plc_bonus->getLevels();
if ($_W['isajax']) {
    if ($_W['ispost']) {
        $data = $_GPC['memberdata'];
        $mdata = array(
            "realname" => trim($data['realname']),
            "mobile" => intval($data['mobile']),
            "weixin" => trim($data['weixin']),
            "bonuslevel" => intval($data['bonus_level']),
            "isagency" => 1
        );
        pdo_update('sz_yi_member', $mdata, array('openid' => $openid, 'uniacid' => $uniacid));
        show_json(1);
    }
}
include $this->template('agency');
