<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 16/11/8
 * Time: 下午12:08
 */

global $_GPC, $_W;
session_start();

$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'index';

$uc = pdo_fetch("SELECT `wx` FROM ".tablename('uni_settings') . " WHERE uniacid = :uniacid", array(':uniacid' => $_W['uniacid']));

$wx = @iunserializer($uc['wx']);

if ($wx['login_switch'] == 1) {
    $AppID = $wx['wx_appid'];

    $AppSecret = $wx['wx_appsecret'];
} else {
    message('请开启微信扫码登录！', referer(), 'error');
}


if ($operation == 'index') {

    $callback  =  $this->createPluginMobileUrl('discuz/login', array('op'=>'register')); //回调地址

//微信登录

//-------生成唯一随机串防CSRF攻击
    $state  = md5(uniqid(rand(), TRUE));
    $_SESSION["wx_state"]    =   $state; //存到SESSION

    $callback = urlencode($callback);

    $wxurl = "https://open.weixin.qq.com/connect/qrconnect?appid=".$AppID."&redirect_uri={$callback}&response_type=code&scope=snsapi_login&state={$state}#wechat_redirect";

    header("Location: $wxurl");
    exit;
} elseif ($operation == 'register') {
    $userinfo = $this->model->userinfo($AppID, $AppSecret);

    $fan = mc_fansinfo($userinfo['openid']);

    if (empty($fan)) {
        if(!is_error($userinfo) && !empty($userinfo)) {
            $userinfo['nickname'] = stripcslashes($userinfo['nickname']);
            if (!empty($userinfo['headimgurl'])) {
                $userinfo['headimgurl'] = rtrim($userinfo['headimgurl'], '0') . 132;
            }
            $userinfo['avatar'] = $userinfo['headimgurl'];
            //$_SESSION['userinfo'] = base64_encode(iserializer($userinfo));
        }

        $record = array(
            'openid' => $userinfo['openid'],
            'uid' => 0,
            'acid' => $_W['acid'],
            'uniacid' => $_W['uniacid'],
            'salt' => random(8),
            'updatetime' => TIMESTAMP,
            'nickname' => stripslashes($userinfo['nickname']),
            'follow' => 0,
            'followtime' => '',
            'unfollowtime' => 0,
            'tag' => base64_encode(iserializer($userinfo))
        );

        if (!isset($unisetting['passport']) || empty($unisetting['passport']['focusreg'])) {
            $default_groupid = pdo_fetchcolumn('SELECT groupid FROM ' .tablename('mc_groups') . ' WHERE uniacid = :uniacid AND isdefault = 1', array(':uniacid' => $_W['uniacid']));
            $data = array(
                'uniacid' => $_W['uniacid'],
                'email' => md5($userinfo['openid']).'@we7.cc',
                'groupid' => $default_groupid,
                'createtime' => TIMESTAMP,
                'nickname' => stripslashes($userinfo['nickname']),
                'avatar' => $userinfo['headimgurl'],
                'gender' => $userinfo['sex'],
                'nationality' => $userinfo['country'],
                'resideprovince' => $userinfo['province'] . '省',
                'residecity' => $userinfo['city'] . '市',
            );

            $data['salt']  = random(8);

            $data['password'] = md5($data['email'] . $data['salt'] . $_W['config']['setting']['authkey']);

            //mc_members
            pdo_insert('mc_members', $data);
            $uid = pdo_insertid();
            $record['uid'] = $uid;
            $_SESSION['uid'] = $uid;
        }

        //mc_mapping_fans
        pdo_insert('mc_mapping_fans', $record);
        $_SESSION['openid'] = $record['openid'];
        $_W['fans'] = $record;
        $_W['fans']['from_user'] = $record['openid'];

        //sz_yi_member
        $member = array(
            'uniacid' => $_W['uniacid'],
            'uid' => $uid,
            'openid' => $userinfo['openid'],
            'realname' =>  '',
            'mobile' => '',
            'nickname' => $userinfo['nickname'],
            'avatar' => $userinfo['avatar'],
            'gender' => $userinfo['sex'],
            'province' => $userinfo['province'],
            'city' => $userinfo['city'],
            'area' => '',
            'createtime' => time(),
            'status' => 1
        );

        pdo_insert('sz_yi_member', $member);
    } else {
        $uid = $fan['uid'];
    }

    //discuz会员注册
    $email = substr(md5($userinfo['openid']), 0, 15) .'@yunzshop.com';
    $pwd = md5(uniqid(mt_rand()));

    mc_init_uc();

    $exist = $this->model->hasId($uid);

    if (empty($exist)) {
        $userinfo['nickname'] = @iconv("utf-8", "gbk", $userinfo['nickname']);
        $this->model->userScanRegister($uid, $userinfo['nickname'] . substr($userinfo['openid'], 0, 4), $email, $pwd);
    }

    $this->model->userLogin($uid);
}
