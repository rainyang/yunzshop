<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 17/2/23
 * Time: 上午11:20
 */

namespace app\frontend\modules\member\services;

use app\common\services\Session;
use app\frontend\modules\member\models\MemberMiniAppModel;

class MemberMiniAppService extends MemberService
{
    const LOGIN_TYPE    = 2;

    public function __construct()
    {}

    public function login()
    {
        include dirname(__FILE__ ) . "/../vendors/wechat/wxBizDataCrypt.php";

        $uniacid = \YunShop::app()->uniacid;

        if (config('app.debug')) {
            $appid = 'wx31002d5db09a6719';
            $secret = '217ceb372d5e3296f064593fe2e7c01e';
        }

        $para = \YunShop::request();

        $data = array(
            'appid' => $appid,
            'secret' => $secret,
            'js_code' => $para['code'],
            'grant_type' => 'authorization_code',
        );

        $url = 'https://api.weixin.qq.com/sns/jscode2session';

        $user_info = \Curl::to($url)
            ->withData($data)
            ->asJsonResponse(true)
            ->get();
        
        $data = '';  //json

        if (!empty($para['info'])) {
            $json_data = json_decode($para['info'], true);

            $pc = new \WXBizDataCrypt($appid, $user_info['session_key']);
            $errCode = $pc->decryptData($json_data['encryptedData'], $json_data['iv'], $data);
        }

        if ($errCode == 0) {
            $json_user = json_decode($data, true);
        } else {
            return show_json(0,'登录认证失败');
        }

        if (!empty($json_user)) {
            $json_user['openid']     = $json_user['openId'];
            $json_user['nickname']   = $json_user['nickName'];
            $json_user['headimgurl'] = $json_user['avatarUrl'];
            $json_user['sex']        = $json_user['gender'];

            //Login
            $member_id = $this->memberLogin($json_user);
            $this->createMiniMember($json_user, ['uniacid'=>$uniacid, 'member_id'=>$member_id]);

            Session::set('member_id', $member_id);

            $random = $this->wx_app_session($user_info);

            $result = array('session' => $random, 'wx_token' =>session_id(), 'uid' => $member_id);

            return show_json(1, $result);
        } else {
            return show_json(0, '获取用户信息失败');
        }
    }

    /**
     * 小程序登录态
     *
     * @param $user_info
     * @return string
     */
    function wx_app_session($user_info)
    {
        if (empty($user_info['session_key']) || empty($user_info['openid'])) {
            return show_json(0,'用户信息有误');
        }

        $random = md5(uniqid(mt_rand()));

        $_SESSION['wx_app'] = array($random => iserializer(array('session_key'=>$user_info['session_key'], 'openid'=>$user_info['openid'])));

        return $random;
    }

    public function createMiniMember($json_user, $arg)
    {
        $user_info = MemberMiniAppModel::getUserInfo($json_user['openid']);

        if (!empty($user_info)) {
            MemberMiniAppModel::updateUserInfo($json_user['openid'],array(
                'nickname' => $json_user['nickname'],
                'avatar' => $json_user['headimgurl'],
                'gender' => $json_user['sex'],
            ));
        } else {
            MemberMiniAppModel::insertData(array(
                'uniacid' => $arg['uniacid'],
                'member_id' => $arg['member_id'],
                'openid' => $json_user['openid'],
                'nickname' => $json_user['nickname'],
                'avatar' => $json_user['headimgurl'],
                'gender' => $json_user['sex'],
            ));
        }
    }
}
