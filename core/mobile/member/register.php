<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$preUrl = $_COOKIE['preUrl'];
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

//访问来自app分享
$from = !empty($_GPC['from']) ? $_GPC['from'] : '';

session_start();

if (m('user')->islogin() != false) {
    header('location: ' . $this->createMobileUrl('member'));
}

//获取APP参数设置
if (is_app()) {
    $setdata = pdo_fetch("select * from " . tablename('sz_yi_sysset') . ' where uniacid=:uniacid limit 1', array(
        ':uniacid' => $_W['uniacid']
    ));
    $set     = unserialize($setdata['sets']);

    $app = $set['app']['base'];
}
if ($_W['isajax']) {
    if ($_W['ispost']) {
        $mobile = !empty($_GPC['mobile']) ? $_GPC['mobile'] : show_json(0, '手机号不能为空！');
        $password = !empty($_GPC['password']) ? $_GPC['password'] : show_json(0, '密码不能为空！');
        $code = !empty($_GPC['code']) ? $_GPC['code'] : show_json(0, '验证码不能为空！');
        if (($_SESSION['codetime']+60*5) < time()) {
            return show_json(0, '验证码已过期,请重新获取');
        }
        if ($_SESSION['code'] != $code) {
            return show_json(0, '验证码错误,请重新获取');
        }
        if ($_SESSION['code_mobile'] != $mobile) {
            return show_json(0, '注册手机号与验证码不匹配！');
        }
        $member = pdo_fetch('select * from ' . tablename('sz_yi_member') . ' where mobile=:mobile and pwd!="" and uniacid=:uniacid limit 1', array(
                        ':uniacid' => $_W['uniacid'],
                        ':mobile' => $mobile
                    ));
        
        if (!empty($member)) {
            return show_json(0, '该手机号已被注册！');
        }
        //使用推荐码 是否开启
        $isreferraltrue = false;

        //判断APP,PC是否开启推荐码功能
        if (is_app()) {
            $isreferral = $app['accept'];
        } else {
            $isreferral = $this->yzShopSet['isreferral'];
        }

        if ($isreferral == 1 && !empty($_GPC['referral'])) {
            $referral = pdo_fetch('select * from ' . tablename('sz_yi_member') . ' where referralsn=:referralsn and uniacid=:uniacid limit 1', array(
                        ':uniacid' => $_W['uniacid'],
                        ':referralsn' => $_GPC['referral']
                    ));
            if (!$referral) {
                return show_json(0, '推荐码无效！');
            } else {
                $isreferraltrue = true;
            }
        }
        
        $openid = pdo_fetchcolumn('select openid from ' . tablename('sz_yi_member') . ' where mobile=:mobile and uniacid=:uniacid limit 1', array(
                        ':uniacid' => $_W['uniacid'],
                        ':mobile' => $mobile
                    ));
        if (empty($openid)) {
            $member_data = array(
                'uniacid' => $_W['uniacid'],
                'uid' => 0,
                'openid' => 'u'.md5($mobile),
                'mobile' => $mobile,
                'pwd' => md5($password),   //md5
                'createtime' => time(),
                'status' => 0,
                'regtype' => 2,
            );

            if (is_app()) {
                $member_data['bindapp'] = 1;
            }

            if (!is_weixin()) {
                $member_data['nickname'] = $mobile;
                $member_data['avatar'] = "http://".$_SERVER ['HTTP_HOST']. '/addons/sz_yi/template/mobile/default/static/images/photo-mr.jpg';
            }

            pdo_insert('sz_yi_member', $member_data);
            $openid = $member_data['openid'];
        } else {
            $member_data = array(
                'pwd' => md5($password),   //md5
                'regtype' => 1,
                'isbindmobile' => 1
            );
            pdo_update('sz_yi_member', $member_data, array("mobile" => $mobile, "uniacid" => $_W['uniacid']));
        }
        
        //使用推荐码 SH20160520172508468878
        if ($isreferraltrue) {
             $member = pdo_fetch('select * from ' . tablename('sz_yi_member') . ' where mobile=:mobile and pwd!="" and uniacid=:uniacid limit 1', array(
                        ':uniacid' => $_W['uniacid'],
                        ':mobile' => $mobile
                    ));
                   
            if (!$member['agentid']) {
                $m_data = array(
                    'agentid' => $referral['id'],
                    'agenttime' => time(),
                    'status' => 1,
                    'isagent' => 1
                );
                if($referral['id'] != 0){
                    $this->upgradeLevelByAgent($referral['id']);
                }
                pdo_update('sz_yi_member', $m_data, array("mobile" => $mobile, "uniacid" => $_W['uniacid']));
                m('member')->responseReferral($this->yzShopSet, $referral, $member);
            }
        }

        $lifeTime = 24 * 3600 * 3;
        session_set_cookie_params($lifeTime);
        @session_start();
        $cookieid = "__cookie_sz_yi_userid_{$_W['uniacid']}";
        setcookie('member_mobile', $mobile);
        setcookie($cookieid, base64_encode($openid));
        if(empty($preUrl))
        {
            $preUrl = $this->createMobileUrl('shop');
        }

        if ($from == 'app') {
            $preUrl = $this->createMobileUrl('shop/download');
        }

        return show_json(1, $preUrl);
    }      
}
include $this->template('member/register');
