<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$openid = m('user')->getOpenid();
$member = m('member')->getMember($openid);
$preUrl = $_COOKIE['preUrl'];
if ($_W['isajax']) {
    if ($_W['ispost']) {
        $mc = $_GPC['memberdata'];
        $isbindmobile = pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_member') . ' where  mobile =:mobile and uniacid=:uniacid and isbindmobile=1', array(':uniacid' => $_W['uniacid'], ':mobile' => $mc['mobile']));
        if(!empty($isbindmobile)){
            return show_json(0, array());
        }

        pdo_update('sz_yi_member',
            array(
                'mobile' => $mc['mobile'],
                'pwd' => md5($mc['password']),
                'isbindmobile' => 1,
            ),
            array(
                'openid' => $openid,
                'uniacid' => $_W['uniacid']
            )
        );
        return show_json(1, array(
            'preurl' => $preUrl
        ));
    }
}
include $this->template('member/editmobile');
