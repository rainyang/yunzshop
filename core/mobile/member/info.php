<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$openid = m('user')->getOpenid();
$member = m('member')->getInfo($openid);
if ($_W['isajax']) {
    if ($_W['ispost']) {
        $memberdata = $_GPC['memberdata'];
        pdo_update('sz_yi_member', $memberdata, array(
            'openid' => $openid,
            'uniacid' => $_W['uniacid']
        ));
        if (!empty($member['uid'])) {
            $mcdata = $_GPC['mcdata'];
            load()->model('mc');
            mc_update($member['uid'], $mcdata);
        }
        show_json(1);
    }
    show_json(1, array(
        'member' => $member
    ));
}
include $this->template('member/info');
