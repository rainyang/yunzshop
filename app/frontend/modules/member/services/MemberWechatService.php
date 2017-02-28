<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/2/23
 * Time: 上午11:21
 */

namespace app\frontend\modules\member\services;

use app\frontend\modules\member\services\MemberMcService;
use app\frontend\modules\member\models\MemberWechatModel;

class MemberWechatService extends MemberMcService
{
    private $_app_id;
    private $_appSecret;

    public function __construct()
    {
        $this->_init();
    }

    private function _init()
    {
        $this->_app_id = '';
        $this->_appSecret = '';
    }

    public function login()
    {
        $callback  =  $this->createPluginMobileUrl('discuz/login', array('op'=>'register')); //回调地址

        //微信登录
        //-------生成唯一随机串防CSRF攻击
        $state  = md5(uniqid(rand(), TRUE));
        $_SESSION["wx_state"]    =   $state; //存到SESSION

        $callback = urlencode($callback);

        $wxurl = "https://open.weixin.qq.com/connect/qrconnect?appid=".$this->_app_id."&redirect_uri={$callback}&response_type=code&scope=snsapi_login&state={$state}#wechat_redirect";

        header("Location: $wxurl");
        exit;
    }

    /**
     * pc端微信登录获取用户信息
     *
     * @return array|mixed|stdClass
     */
    public function getUserInfo()
    {
        global $_W;

        if ($_GET['state'] != $_SESSION["wx_state"]) {
            exit("5001");
        }

        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $this->_app_id . '&secret=' . $this->_appSecret . '&code=' . $_GET['code'] . '&grant_type=authorization_code';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $url);
        $json = curl_exec($ch);
        curl_close($ch);
        $arr = json_decode($json, 1);

        $url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $arr['access_token'] . '&openid=' . $arr['openid'] . '&lang=zh_CN';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $url);
        $json = curl_exec($ch);
        curl_close($ch);
        $arr = json_decode($json, 1);

        return $arr;
    }
}