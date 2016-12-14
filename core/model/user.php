<?php
/*=============================================================================
#     FileName: user.php
#         Desc: 用户类
#       Author: Yunzhong - http://www.yunzshop.com
#        Email: 913768135@qq.com
#     HomePage: http://www.yunzshop.com
#      Version: 0.0.1
#   LastChange: 2016-02-05 02:35:26
#      History:
=============================================================================*/
if (!defined('IN_IA')) {
    exit('Access Denied');
}
class Sz_DYi_User
{
    private $sessionid;
    public function __construct()
    {
        global $_W;
        $this->sessionid = "__cookie_sz_yi_201507200000_{$_W['uniacid']}";
    }
    function getOpenid()
    {
        $userinfo = $this->getInfo(false, true);
        return $userinfo['openid'];
    }
    function getPerOpenid()
    {
        global $_W, $_GPC;

        $lifeTime = 24 * 3600 * 3;
        session_set_cookie_params($lifeTime);
        @session_start();
        $cookieid = "__cookie_sz_yi_userid_{$_W['uniacid']}";

        $openid   = base64_decode($_COOKIE[$cookieid]);

        if (!empty($openid)) {
            return $openid;
        }
        load()->func('communication');
        $appId        = $_W['account']['key'];
        $appSecret    = $_W['account']['secret'];
        $access_token = "";
        $code         = $_GPC['code'];
        $url          = $_W['siteroot'] . 'app/index.php?' . $_SERVER['QUERY_STRING'];
        if (empty($code)) {
            $authurl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $appId . "&redirect_uri=" . urlencode($url) . "&response_type=code&scope=snsapi_base&state=123#wechat_redirect";
            header('location: ' . $authurl);
            exit();
        } else {
            $tokenurl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $appId . "&secret=" . $appSecret . "&code=" . $code . "&grant_type=authorization_code";
            $resp     = ihttp_get($tokenurl);
            $token    = @json_decode($resp['content'], true);
            if (!empty($token) && is_array($token) && $token['errmsg'] == 'invalid code') {
                $authurl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $appId . "&redirect_uri=" . urlencode($url) . "&response_type=code&scope=snsapi_base&state=123#wechat_redirect";
                header('location: ' . $authurl);
                exit();
            }
            if (is_array($token) && !empty($token['openid'])) {
                $access_token = $token['access_token'];
                $openid       = $token['openid'];
                setcookie($cookieid, base64_encode($openid));
            } else {
                $querys = explode('&', $_SERVER['QUERY_STRING']);
                $newq   = array();
                foreach ($querys as $q) {
                    if (!strexists($q, 'code=') && !strexists($q, 'state=') && !strexists($q, 'from=') && !strexists($q, 'isappinstalled=')) {
                        $newq[] = $q;
                    }
                }
                $rurl    = $_W['siteroot'] . 'app/index.php?' . implode('&', $newq);
                $authurl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $appId . "&redirect_uri=" . urlencode($rurl) . "&response_type=code&scope=snsapi_base&state=123#wechat_redirect";
                header('location: ' . $authurl);
                exit;
            }
        }
        return $openid;
    }

    function isLogin(){
        global $_W, $_GPC;

        @session_start();
        $cookieid = "__cookie_sz_yi_userid_{$_W['uniacid']}";
        $openid   = base64_decode($_COOKIE[$cookieid]);

        /**
         * app端通过token验证用户身份
         */
        if  (empty($_SERVER['HTTP_USER_AGENT']) && empty($openid) && $_GPC['token']) {
            $openid = $_GPC['token'];
        }

        if (!empty($openid)) {
            //微信端绑定手机号,导致原来openid不存在,需重新登录
            if (is_app()) {
                $result = pdo_fetch("select id from " . tablename('sz_yi_member') . ' WHERE  openid=:openid and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));

                if (empty($result)) {
                    setcookie($cookieid, '', time()-1);
                    header("Location:/app/index.php?i=" . $_W['uniacid'] . "&c=entry&p=login&do=member&m=sz_yi");
                    exit;
                }
            }
            return $openid;
        }
        return false;
    }

    function getUserInfo(){
        global $_W, $_GPC;
        //用于测试直接返回用户信息
        /*$userinfo = array(
                'openid' => 'oh6uVxM1tlBQiP8diuFHbZfbOP30',
                'nickname' => '杨明',
                'headimgurl' => 'http://wx.qlogo.cn/mmopen/ajNVdqHZLLATXmBR0oJgXhJRvS7rrr6ay25CWblqAA5kn8OribpHQXHVl8DSsEUJyOhvkq6TvBW0z1861oST7vg/132',
            );
        return $userinfo;*/
        if($_GPC['p'] == 'return' && $_GPC["method"]=='task'){
            return;
        }
        if($_GPC['p'] == 'recharge' && $_GPC["method"]=='mobile_data_back'){
            return;
        }
        if($_GPC['p'] == 'yunbi' && $_GPC["method"]=='task'){
            return;
        }
        if($_GPC['p'] == 'ranking' && $_GPC["method"]=='commission'){
            return;
        }
        if($_GPC['p'] == 'area' && $_GPC["method"]=='area_list'){
            return;
        }
        if($_GPC['p'] == 'area' && $_GPC["method"]=='area'){
            return;
        }
        if($_GPC['p'] == 'area' && $_GPC["method"]=='area_detail'){
            return;
        }
        if($_GPC['p'] == 'article' && $_GPC["method"]=='article_pc'){
            return;
        }
        if($_GPC['p'] == 'verify' && $_GPC["method"]=='store_index'){
            return;
        }
        if($_GPC['p'] == 'verify' && $_GPC["method"]=='store_list'){
            return;
        }
        if($_GPC['p'] == 'verify' && $_GPC["method"]=='store_detail'){
            return;
        }
        //需要登陆的P方法                  
        $needLoginPList = array('address', 'commission','cart');

        //不需要登陆的P方法
        $noLoginList = array('category', 'login' ,'receive', 'close', 'designer', 'register', 'sendcode', 'bindmobile', 'forget', 'home', 'fund', 'discuz');

        //不需要登陆的do方法
        $noLoginDoList = array('shop', 'login', 'register');

        //首页不用判断是否登陆
        if(!$_GPC['p'] && $_GPC["do"]=='shop'){
            return;
        }

        //帮助中心不需要登录
        if (!empty($_GPC['is_helper']) && $_GPC['p'] == 'article') {
            return;
        }
        /*
        if($_GPC["c"]=='entry'){
            return;
        }
         */
        //需要登陆
        if((!in_array($_GPC["p"], $noLoginList) && !in_array($_GPC["do"], $noLoginDoList)) or (in_array($_GPC["p"], $needLoginPList))){
            //小店不需要登陆，否则分享出去别人不能直接看到
            if(($_GPC['method'] != 'myshop') or ($_GPC['c'] != 'entry')){
                $openid = $this->isLogin();
                if(!$openid && $_GPC['p'] != 'cart'){  //未登录
                    if($_GPC['do'] != 'runtasks'){
                        setcookie('preUrl', $_W['siteurl']);
                    }
                    $mid = ($_GPC['mid']) ? "&mid=".$_GPC['mid'] : "";
                    $url = "/app/index.php?i={$_W['uniacid']}&c=entry&p=login&do=member&m=sz_yi".$mid;

                    redirect($url);
                }
                else{
                    $userinfo = array(
                        'openid' => $openid,
                        //'nickname' => '小萝莉',
                        'headimgurl' => '',
                    );

                    return $userinfo;
                }
            }
        } elseif (is_app() && $_GPC["p"] == 'index' && $_GPC["do"] == 'shop') {
            $openid = $this->isLogin();
            $userinfo = array(
                'openid' => $openid,
                //'nickname' => '小萝莉',
                'headimgurl' => '',
            );

            return $userinfo;
        }
    }

    function getInfo($base64 = false, $debug = false)
    {
        global $_W, $_GPC;
        if(!is_weixin()&&!is_app_api() ){
            return $this->getUserInfo();
        }
        if(is_app_api()){
            if(in_array($_GET['api'],array('index/Index','category/Index','goods/Detail','member/Register','goods/Display','member/SentCode'))){
                return false;
            }
        }
        $userinfo = array();
        if (SZ_YI_DEBUG) {
            $userinfo = array(
                'openid' => 'oVwSVuJXB7lGGc93d0gBXQ_h-czc',
                'nickname' => '小萝莉',
                'headimgurl' => '',
                'province' => '香港',
                'city' => '九龙'
            );
        } else {
            //var_dump($_GPC['directopenid']);exit;
            if (empty($_GPC['directopenid'])) {
                $userinfo = mc_oauth_userinfo();
            } else {
                $userinfo = array(
                    'openid' => $this->getPerOpenid()
                );
            }
            //$need_openid = true;
            $need_openid = false;
            if ($_W['container'] != 'wechat') {
                if ($_GPC['do'] == 'order' && $_GPC['p'] == 'pay') {
                    $need_openid = false;
                }
                if ($_GPC['do'] == 'member' && $_GPC['p'] == 'recharge') {
                    $need_openid = false;
                }
				if ($_GPC['do'] == 'plugin' && $_GPC['p'] == 'article' && $_GPC['preview'] == '1') {
					$need_openid = false;
				}
            }

            /*
            if (empty($userinfo['openid']) && $need_openid) {
                die("<!DOCTYPE html>
                <html>
                    <head>
                        <meta name='viewport' content='width=device-width, initial-scale=1, user-scalable=0'>
                        <title>抱歉，出错了</title><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1, user-scalable=0'><link rel='stylesheet' type='text/css' href='https://res.wx.qq.com/connect/zh_CN/htmledition/style/wap_err1a9853.css'>
                    </head>
                    <body>
                    <div class='page_msg'><div class='inner'><span class='msg_icon_wrp'><i class='icon80_smile'></i></span><div class='msg_content'><h4>请在微信客户端打开链接</h4></div></div></div>
                    </body>
                </html>");
            }
             */
        }
        if ($base64) {
            return urlencode(base64_encode(json_encode($userinfo)));
        }
        return $userinfo;
    }
    function oauth_info()
    {
        global $_W, $_GPC;
        if ($_W['container'] != 'wechat') {
            if ($_GPC['do'] == 'order' && $_GPC['p'] == 'pay') {
                return array();
            }
            if ($_GPC['do'] == 'member' && $_GPC['p'] == 'recharge') {
                return array();
            }
        }
        $lifeTime = 24 * 3600 * 3;
        session_set_cookie_params($lifeTime);
        @session_start();
        $sessionid = "__cookie_sz_yi_201507100000_{$_W['uniacid']}";
        $session   = json_decode(base64_decode($_SESSION[$sessionid]), true);
        $openid    = is_array($session) ? $session['openid'] : '';
        $nickname  = is_array($session) ? $session['openid'] : '';
        if (!empty($openid)) {
            return $session;
        }
        load()->func('communication');
        $appId        = $_W['account']['key'];
        $appSecret    = $_W['account']['secret'];
        $access_token = "";
        $code         = $_GPC['code'];
        $url          = $_W['siteroot'] . 'app/index.php?' . $_SERVER['QUERY_STRING'];
        if (empty($code)) {
            $authurl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $appId . "&redirect_uri=" . urlencode($url) . "&response_type=code&scope=snsapi_userinfo&state=123#wechat_redirect";
            header('location: ' . $authurl);
            exit();
        } else {
            $tokenurl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $appId . "&secret=" . $appSecret . "&code=" . $code . "&grant_type=authorization_code";
            $resp     = ihttp_get($tokenurl);
            $token    = @json_decode($resp['content'], true);
            if (!empty($token) && is_array($token) && $token['errmsg'] == 'invalid code') {
                $authurl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $appId . "&redirect_uri=" . urlencode($url) . "&response_type=code&scope=snsapi_userinfo&state=123#wechat_redirect";
                header('location: ' . $authurl);
                exit();
            }
            if (empty($token) || !is_array($token) || empty($token['access_token']) || empty($token['openid'])) {
                die('获取token失败,请重新进入!');
            } else {
                $access_token = $token['access_token'];
                $openid       = $token['openid'];
            }
        }
        $infourl  = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid . "&lang=zh_CN";
        $resp     = ihttp_get($infourl);
        $userinfo = @json_decode($resp['content'], true);
        if (isset($userinfo['nickname'])) {
            $_SESSION[$sessionid] = base64_encode(json_encode($userinfo));
            return $userinfo;
        } else {
            die('获取用户信息失败，请重新进入!');
        }
    }
    function followed($openid = '')
    {
        global $_W;
        $followed = !empty($openid);
        if ($followed) {
            $mf       = pdo_fetch("select follow from " . tablename('mc_mapping_fans') . " where openid=:openid and uniacid=:uniacid limit 1", array(
                ":openid" => $openid,
                ':uniacid' => $_W['uniacid']
            ));
            $followed = $mf['follow'] == 1;
        }
        return $followed;
    }
}
