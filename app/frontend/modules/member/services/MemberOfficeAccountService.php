<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 17/2/22
 * Time: 下午4:44
 */

namespace app\frontend\modules\member\services;

use app\common\helpers\Client;
use app\common\helpers\Url;
use app\common\models\AccountWechats;
use app\common\models\Member;
use app\common\models\MemberGroup;
use app\common\models\MemberShopInfo;
use app\common\services\Session;
use app\frontend\models\McGroupsModel;
use app\frontend\modules\member\models\McMappingFansModel;
use app\frontend\modules\member\models\MemberModel;
use app\frontend\modules\member\models\MemberUniqueModel;
use app\frontend\modules\member\models\SubMemberModel;

class MemberOfficeAccountService extends MemberService
{
    const LOGIN_TYPE = '1';

    public function __construct()
    {
    }

    public function login($params = [])
    {
        $member_id = 0;

        $uniacid = \YunShop::app()->uniacid;
        $code = \YunShop::request()->code;

        $account = AccountWechats::getAccountByUniacid($uniacid);
        $appId = $account->key;
        $appSecret = $account->secret;

        if ($params['scope'] == 'user_info') {
            \Log::debug('user info callback');
            $callback = Url::absoluteApi('member.login.index', ['type' => 1, 'scope' => 'user_info']);


        } else {
            \Log::debug('default');
            $callback = ($_SERVER['REQUEST_SCHEME'] ? $_SERVER['REQUEST_SCHEME'] : 'http')  . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        }

        $state = 'yz-' . session_id();

        if (!Session::get('member_id')) {
            if ($params['scope'] == 'user_info' || \YunShop::request()->scope == 'user_info') {
                $authurl = $this->_getAuthBaseUrl($appId, $callback, $state);
            } else {
                $authurl = $this->_getAuthUrl($appId, $callback, $state);
            }
        } else {
            $authurl = $this->_getAuthBaseUrl($appId, $callback, $state);
        }

        $tokenurl = $this->_getTokenUrl($appId, $appSecret, $code);

        if (!empty($code)) {
            $redirect_url = $this->_getClientRequestUrl();

            $token = \Curl::to($tokenurl)
                ->asJsonResponse(true)
                ->get();

            if (!empty($token) && !empty($token['errmsg']) && $token['errmsg'] == 'invalid code') {
                return show_json(5, 'token请求错误');
            }

            $userinfo = $this->getUserInfo($appId, $appSecret, $token);

            if (is_array($userinfo) && !empty($userinfo['errcode'])) {
                \Log::debug('微信登陆授权失败');
                return show_json('-3', '微信登陆授权失败');
            }

            //Login
            $member_id = $this->memberLogin($userinfo);

            \YunShop::app()->openid = $userinfo['openid'];
            Session::set('member_id', $member_id);
        } else {
            $this->_setClientRequestUrl();

            redirect($authurl)->send();
            exit;
        }

        if (\YunShop::request()->scope == 'user_info') {
            return show_json(1, 'user_info_api');
        } else {
            redirect($redirect_url)->send();
        }
    }

    /**
     * 公众号加入开放平台
     *
     * @param $uniacid
     * @param $userinfo
     * @return array|int|mixed
     */
    public function unionidLogin($uniacid, $userinfo, $upperMemberId = NULL)
    {
        $member_id = 0;

        $UnionidInfo = MemberUniqueModel::getUnionidInfo($uniacid, $userinfo['unionid'])->first();
        $mc_mapping_fans_model = McMappingFansModel::getUId($userinfo['openid']);

        if (!empty($UnionidInfo)) {
            $UnionidInfo = $UnionidInfo->toArray();
        }

        if ($mc_mapping_fans_model) {
            $member_model = Member::getMemberById($mc_mapping_fans_model->uid);
            $member_shop_info_model = MemberShopInfo::getMemberShopInfo($mc_mapping_fans_model->uid);

            $member_id = $mc_mapping_fans_model->uid;
        }

        if (!empty($UnionidInfo['unionid']) && !empty($member_model) && !empty($mc_mapping_fans_model) && !empty($member_shop_info_model)) {
            $types = explode('|', $UnionidInfo['type']);
            $member_id = $UnionidInfo['member_id'];

            if (!in_array(self::LOGIN_TYPE, $types)) {
                //更新ims_yz_member_unique表
                MemberUniqueModel::updateData(array(
                    'unique_id' => $UnionidInfo['unique_id'],
                    'type' => $UnionidInfo['type'] . '|' . self::LOGIN_TYPE
                ));
            }

            $this->updateMemberInfo($member_id, $userinfo);
        } else {
            \Log::debug('添加新会员');

            if (!empty($member_model) && empty($mc_mapping_fans_model)) {
                $member_id = $member_model->uid;

                $this->updateMainInfo($member_id, $userinfo);
                $this->addFansInfo($member_id, $uniacid, $userinfo);
            } else {
                $member_id = $this->addMemberInfo($uniacid, $userinfo);

                if ($member_id === false) {
                    return show_json(8, '保存用户信息失败');
                }
            }

            $this->addSubMemberInfo($uniacid, $member_id);

            //添加ims_yz_member_unique表
            $this->addMemberUnionid($uniacid, $member_id, $userinfo['unionid']);

            //生成分销关系链
            if ($upperMemberId) {
                Member::createRealtion($member_id, $upperMemberId);
            } else {
                Member::createRealtion($member_id);
            }
        }

        return $member_id;
    }

    /**
     * 公众号为加入开放平台
     *
     * @param $uniacid
     * @param $userinfo
     * @return array|int|mixed
     */
    public function openidLogin($uniacid, $userinfo, $upperMemberId = NULL)
    {
        $member_id = 0;
        
        $fans_mode = McMappingFansModel::getUId($userinfo['openid']);

        if ($fans_mode) {
            $member_model = Member::getMemberById($fans_mode->uid);
            $member_shop_info_model = MemberShopInfo::getMemberShopInfo($fans_mode->uid);

            $member_id = $fans_mode->uid;
        }

        if (!empty($member_model) && !empty($fans_mode) && !empty($member_shop_info_model)) {
            \Log::debug('微信登陆更新');

            $this->updateMemberInfo($member_id, $userinfo);
        } else {
            \Log::debug('添加新会员');

            if (!empty($member_model) && empty($fans_mode)) {
                $member_id = $member_model->uid;

                $this->updateMainInfo($member_id, $userinfo);
                $this->addFansInfo($member_id, $uniacid, $userinfo);
            } else {
                $member_id = $this->addMemberInfo($uniacid, $userinfo);
            }

            if ($member_id === false) {
                return show_json(8, '保存用户信息失败');
            }

            $this->addSubMemberInfo($uniacid, $member_id);

            //生成分销关系链
            if ($upperMemberId) {
                Member::createRealtion($member_id, $upperMemberId);
            } else {
                Member::createRealtion($member_id);
            }
        }

        return $member_id;
    }

    /**
     * 获取用户信息
     *
     * @param $appId
     * @param $appSecret
     * @param $token
     * @return mixed
     */
    public function getUserInfo($appId, $appSecret, $token)
    {
        $global_access_token_url = $this->_getAccessToken($appId, $appSecret);

        $global_token = \Curl::to($global_access_token_url)
            ->asJsonResponse(true)
            ->get();

        $global_userinfo_url = $this->_getInfo($global_token['access_token'], $token['openid']);

        $user_info = \Curl::to($global_userinfo_url)
            ->asJsonResponse(true)
            ->get();

        if (0 == $user_info['subscribe']) {
            $userinfo_url = $this->_getUserInfoUrl($token['access_token'], $token['openid']);

            $user_info = \Curl::to($userinfo_url)
                ->asJsonResponse(true)
                ->get();

            $user_info['subscribe'] = 0;
        }

        return $user_info;
    }

    /**
     * 会员基础表操作
     *
     * @param $uniacid
     * @param $userinfo
     * @return mixed
     */
    public function addMemberInfo($uniacid, $userinfo)
    {
        //添加mc_members表
        $default_group = McGroupsModel::getDefaultGroupId();
        $uid = MemberModel::insertData($userinfo, array(
            'uniacid' => $uniacid,
            'groupid' => $default_group->groupid
        ));

        //添加mapping_fans表
        McMappingFansModel::insertData($userinfo, array(
            'uid' => $uid,
            'acid' => $uniacid,
            'uniacid' => $uniacid,
            'salt' => Client::random(8),
        ));

        return $uid;
    }

    /**
     * 会员辅助表操作
     *
     * @param $uniacid
     * @param $member_id
     */
    public function addSubMemberInfo($uniacid, $member_id)
    {
        //添加yz_member表
        $default_sub_group_id = MemberGroup::getDefaultGroupId()->first();

        if (!empty($default_sub_group_id)) {
            $default_subgroup_id = $default_sub_group_id->id;
        } else {
            $default_subgroup_id = 0;
        }

        SubMemberModel::insertData(array(
            'member_id' => $member_id,
            'uniacid' => $uniacid,
            'group_id' => $default_subgroup_id,
            'level_id' => 0,
        ));
    }

    /**
     * 会员关联表操作
     *
     * @param $uniacid
     * @param $member_id
     * @param $unionid
     */
    public function addMemberUnionid($uniacid, $member_id, $unionid)
    {
        MemberUniqueModel::insertData(array(
            'uniacid' => $uniacid,
            'unionid' => $unionid,
            'member_id' => $member_id,
            'type' => self::LOGIN_TYPE
        ));
    }

    /**
     * 更新微信用户信息
     *
     * @param $member_id
     * @param $userinfo
     */
    public function updateMemberInfo($member_id, $userinfo)
    {
        //更新mc_members
        $mc_data = array(
            'nickname' => stripslashes($userinfo['nickname']),
            'avatar' => $userinfo['headimgurl'],
            'gender' => $userinfo['sex'],
            'nationality' => $userinfo['country'],
            'resideprovince' => $userinfo['province'] . '省',
            'residecity' => $userinfo['city'] . '市'
        );
        MemberModel::updataData($member_id, $mc_data);

        //更新mapping_fans
        $record = array(
            'openid' => $userinfo['openid'],
            'nickname' => stripslashes($userinfo['nickname']),
            'follow' => $userinfo['subscribe'],
            'tag' => base64_encode(serialize($userinfo))
        );
        McMappingFansModel::updateData($member_id, $record);
    }

    /**
     * 更新mc_members
     *
     * @param $member_id
     * @param $userinfo
     */
    public function updateMainInfo($member_id, $userinfo)
    {
        //更新mc_members
        $mc_data = array(
            'nickname' => stripslashes($userinfo['nickname']),
            'avatar' => $userinfo['headimgurl'],
            'gender' => $userinfo['sex'],
            'nationality' => $userinfo['country'],
            'resideprovince' => $userinfo['province'] . '省',
            'residecity' => $userinfo['city'] . '市'
        );
        MemberModel::updataData($member_id, $mc_data);
    }

    /**
     * 添加粉丝表
     *
     * @param $uid
     * @param $uniacid
     * @param $userinfo
     * @return mixed
     */
    public function addFansInfo($uid, $uniacid, $userinfo)
    {
        //添加mapping_fans表
        McMappingFansModel::insertData($userinfo, array(
            'uid' => $uid,
            'acid' => $uniacid,
            'uniacid' => $uniacid,
            'salt' => Client::random(8),
        ));

        return $uid;
    }

    /**
     * 过滤微信用户名特殊符号
     *
     * @param $userinfo
     * @return mixed
     */
    private function filteNickname($userinfo)
    {
        $patten = "#(\\\ud[0-9a-f][3])|(\\\ue[0-9a-f]{3})#ie";

        $nickname = json_encode($userinfo['nickname']);
        $nickname = preg_replace($patten, "", $nickname);

        return json_decode($nickname);
    }

    /**
     * 用户验证授权 api
     *
     * snsapi_userinfo
     *
     * @param $appId
     * @param $url
     * @param $state
     * @return string
     */
    private function _getAuthUrl($appId, $url, $state)
    {
       return "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $appId . "&redirect_uri=" . urlencode($url) . "&response_type=code&scope=snsapi_userinfo&state={$state}#wechat_redirect";
    }

    /**
     *
     * 静默获取用户信息
     *
     * snsapi_base
     *
     * @param $appId
     * @param $url
     * @param $state
     * @return string
     */
    private function _getAuthBaseUrl($appId, $url, $state)
    {
        return "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $appId . "&redirect_uri=" . urlencode($url) . "&response_type=code&scope=snsapi_base&state={$state}#wechat_redirect";
    }

    /**
     * 获取token api
     *
     * @param $appId
     * @param $appSecret
     * @param $code
     * @return string
     */
    private function _getTokenUrl($appId, $appSecret, $code)
    {
       return "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $appId . "&secret=" . $appSecret . "&code=" . $code . "&grant_type=authorization_code";
    }

    /**
     * 获取用户信息 api
     * @param $accesstoken
     * @param $openid
     * @return string
     */
    private function _getUserInfoUrl($accesstoken, $openid)
    {
        return "https://api.weixin.qq.com/sns/userinfo?access_token={$accesstoken}&openid={$openid}&lang=zh_CN";
    }

    /**
     * 获取全局ACCESS TOKEN
     * @return string
     */
    private function _getAccessToken($appId, $appSecret)
    {
        return 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appId . '&secret=' . $appSecret;
    }

    /**
     * 获取用户信息
     *
     * 是否关注公众号
     *
     * @param $accesstoken
     * @param $openid
     * @return string
     */
    private function _getInfo($accesstoken, $openid)
    {
        return 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $accesstoken . '&openid=' . $openid;
    }

    /**
     * 设置客户端请求地址
     *
     * @return string
     */
    private function _setClientRequestUrl()
    {
        if (\YunShop::request()->yz_redirect) {
           Session::set('client_url', base64_decode(\YunShop::request()->yz_redirect));
        } else {
            Session::set('client_url', '');
        }
    }

    /**
     * 获取客户端地址
     *
     * @return mixed
     */
    private function _getClientRequestUrl()
    {
        return Session::get('client_url');
    }

    /**
     * 登陆处理
     *
     * @param $userinfo
     *
     * @return integer
     */
    public function memberLogin($userinfo, $upperMemberId = NULL)
    {
        if (is_array($userinfo) && !empty($userinfo['unionid'])) {
            $member_id = $this->unionidLogin(\YunShop::app()->uniacid, $userinfo, $upperMemberId);
        } elseif (is_array($userinfo) && !empty($userinfo['openid'])) {
            $member_id = $this->openidLogin(\YunShop::app()->uniacid, $userinfo, $upperMemberId);
        }

        $mid = $upperMemberId ?: Member::getMid();

        //发展下线
        Member::chkAgent($member_id, $mid);

        return $member_id;
    }
}
