<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;

$preUrl = $_COOKIE['preUrl'];
if ($_W['isajax']) {
    if ($_W['ispost']) {
        $mc = $_GPC['memberdata'];
        $info = pdo_fetch('select * from ' . tablename('sz_yi_member') . ' where  mobile=:mobile and uniacid=:uniacid and pwd=:pwd limit 1', array(
                ':uniacid' => $_W['uniacid'],
                ':mobile' => $mc['mobile'],
                ':pwd' => md5($mc['password']),
            ));
        //pdo_debug();

        if($info){
            $lifeTime = 24 * 3600 * 3;
            session_set_cookie_params($lifeTime);
            @session_start();
            $cookieid = "__cookie_sz_yi_userid_{$_W['uniacid']}";
            setcookie($cookieid, base64_encode($info['openid']));
            show_json(1, array(
                'preurl' => $preUrl
            ));
        }
        else{
            show_json(0);
        }
    }
}
include $this->template('member/login');
