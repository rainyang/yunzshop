<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/2/22
 * Time: 下午4:44
 */

namespace app\frontend\modules\member\services;

use app\common\helpers\Client;
use app\common\models\AccountWechats;
use app\common\models\Member;
use app\common\models\MemberGroup;
use app\common\models\MemberLevel;
use app\common\models\MemberShopInfo;
use app\common\services\Session;
use app\frontend\models\McGroupsModel;
use app\frontend\modules\member\models\McMappingFansModel;
use app\frontend\modules\member\models\MemberModel;
use app\frontend\modules\member\models\MemberUniqueModel;
use app\frontend\modules\member\models\SubMemberModel;

class MemberOfficeAccountService extends MemberService
{
    const LOGIN_TYPE    = '1';

    public function __construct()
    {}

    public function login($params = [])
    {
        $member_id = 0;

        $uniacid      = \YunShop::app()->uniacid;
        $code         = \YunShop::request()->code;

        $account      = AccountWechats::getAccountByUniacid($uniacid);
        $appId        = $account->key;
        $appSecret    = $account->secret;

        if ($params['scope'] == 'user_info') {
            \Log::debug('user info callback');
            $callback     = 'http://test.yunzshop.com/addons/yun_shop/api.php?i=2&route=member.login.index&type=1&scope=user_info';

        } else {
            \Log::debug('default');
            $callback     = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        }

        \Log::debug('微信登陆回调地址', $callback);

        $state = 'yz-' . session_id();

        if (!Session::get('member_id')) {
            \Log::debug('scope', $params['scope']);

            if ($params['scope']  == 'user_info' || \YunShop::request()->scope == 'user_info') {
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

            $userinfo_url = $this->_getUserInfoUrl($token['access_token'], $token['openid']);

            $userinfo = \Curl::to($userinfo_url)
                ->asJsonResponse(true)
                ->get();

            if (is_array($userinfo) && !empty($userinfo['errcode'])) {
                \Log::debug('微信登陆授权失败', $userinfo);
                return show_json('-3', '微信登陆授权失败');
            }

            \Log::debug('userinfo', $userinfo);

            if (is_array($userinfo) && !empty($userinfo['unionid'])) {
                $member_id = $this->unionidLogin($uniacid, $userinfo);
            } elseif  (is_array($userinfo) && !empty($userinfo['openid'])) {
                $member_id = $this->openidLogin($uniacid, $userinfo);
            }

            \Log::debug('officaccount mid', \YunShop::request()->mid);
            //检查下线
            Member::chkAgent($member_id);

            \Log::debug('uid', $member_id);

            \YunShop::app()->openid = $userinfo['openid'];
            Session::set('member_id', $member_id);
        } else {
            \Log::debug('获取code', $authurl);
            $this->_setClientRequestUrl();

            redirect($authurl)->send();
            exit;
        }

        if (\YunShop::request()->scope == 'user_info') {
            return show_json(1, 'user_info_api');
        } else {
            \Log::debug('微信登陆成功跳转地址',$redirect_url);
            redirect($redirect_url)->send();
        }
    }

    public function unionidLogin($uniacid, $userinfo)
    {
        $member_id = 0;
        $userinfo['nickname'] = $this->filteNickname($userinfo);

        $UnionidInfo = MemberUniqueModel::getUnionidInfo($uniacid, $userinfo['unionid'])->first();

        if (!empty($UnionidInfo)) {
            $UnionidInfo = $UnionidInfo->toArray();
        }

        if (!empty($UnionidInfo['unionid'])) {
            $types = explode('|', $UnionidInfo['type']);
            $member_id = $UnionidInfo['member_id'];

            if (!in_array(self::LOGIN_TYPE, $types)) {
                //更新ims_yz_member_unique表
                MemberUniqueModel::updateData(array(
                    'unique_id'=>$UnionidInfo['unique_id'],
                    'type' => $UnionidInfo['type'] . '|' . self::LOGIN_TYPE
                ));
            }

            if (MemberShopInfo::isBlack($member_id)) {
                return show_json(-1, '黑名单用户，请联系管理员');
            }

            $this->updateMemberInfo($member_id, $userinfo);
        } else {
            \Log::debug('添加新会员');

            $mc_mapping_fans_model = McMappingFansModel::getUId($userinfo['openid']);

            if ($mc_mapping_fans_model->uid) {
                $member_id = $mc_mapping_fans_model->uid;

                $this->updateMemberInfo($member_id, $userinfo);
            } else {
                $member_id = $this->addMemberInfo($uniacid, $userinfo);

                if ($member_id === false) {
                    return show_json(8, '保存用户信息失败');
                }

                $this->addSubMemberInfo($uniacid, $member_id);

                //添加ims_yz_member_unique表
                $this->addMemberUnionid($uniacid, $member_id, $userinfo['unionid']);

                //生成分销关系链
                Member::createRealtion($member_id);
            }
        }

        return $member_id;
    }

    public function openidLogin($uniacid, $userinfo)
    {
        $member_id = 0;
        $userinfo['nickname'] = $this->filteNickname($userinfo);
        $fans_mode = McMappingFansModel::getUId($userinfo['openid']);

        if ($fans_mode) {
            $member_model = Member::getMemberById($fans_mode->uid);
            $member_shop_info_model = MemberShopInfo::getMemberShopInfo($fans_mode->uid);

            $member_id = $fans_mode->uid;
        }

        \Log::debug('粉丝', $fans_mode->uid);

        if ((!empty($member_model)) && (!empty($fans_mode) && !empty($member_shop_info_model))) {
            \Log::debug('微信登陆更新');

            if (MemberShopInfo::isBlack($member_id)) {
                return show_json(-1, '黑名单用户，请联系管理员');
            }

            $this->updateMemberInfo($member_id, $userinfo);
        } else {
            \Log::debug('添加新会员');

            if ($fans_mode->uid) {
                $this->updateMemberInfo($member_id, $userinfo);
            } else {
                $member_id = $this->addMemberInfo($uniacid, $userinfo);

                if ($member_id === false) {
                    return show_json(8, '保存用户信息失败');
                }

                $this->addSubMemberInfo($uniacid, $member_id);

                //生成分销关系链
                Member::createRealtion($member_id);
            }
        }

        return $member_id;
    }

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

    public function addSubMemberInfo($uniacid, $member_id)
    {
        //添加yz_member表
        $default_sub_group_id = MemberGroup::getDefaultGroupId()->first();
        $default_sub_level_id = MemberLevel::getDefaultLevelId()->first();

        if (!empty($default_sub_group_id)) {
            $default_subgroup_id = $default_sub_group_id->id;
        } else {
            $default_subgroup_id = 0;
        }

        if (!empty($default_sub_level_id)) {
            $default_sublevel_id = $default_sub_level_id->id;
        } else {
            $default_sublevel_id = 0;
        }

        SubMemberModel::insertData(array(
            'member_id' => $member_id,
            'uniacid' => $uniacid,
            'group_id' => $default_subgroup_id,
            'level_id' => $default_sublevel_id,
        ));
    }

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
            'tag' => base64_encode(serialize($userinfo))
        );
        McMappingFansModel::updateData($member_id, $record);
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
}
