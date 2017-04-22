<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/2/22
 * Time: 下午4:44
 */

namespace app\frontend\modules\member\services;

use app\common\events\member\RegisterByAgent;
use app\common\facades\Setting;
use app\common\helpers\Client;
use app\common\models\AccountWechats;
use app\common\models\McMappingFans;
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
        $uniacid      = \YunShop::app()->uniacid;
        $code         = \YunShop::request()->code;

        $account      = AccountWechats::getAccountByUniacid($uniacid);
        $appId        = $account->key;
        $appSecret    = $account->secret;

        $callback     = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        \Log::debug('微信登陆回调地址', $callback);

        $state = 'yz-' . session_id();

        if (!Session::get('member_id')) {
            if (!empty($params) && $params['scope'] == 'user_info') {
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

            if (is_array($userinfo) && !empty($userinfo['openid'])) {
                $patten = "#(\\\ud[0-9a-f][3])|(\\\ue[0-9a-f]{3})#ie";
                $tmpStr = json_encode($userinfo['nickname']);
                $tmpStr = preg_replace($patten, "", $tmpStr);
                $userinfo['nickname'] = json_decode($tmpStr);

                \YunShop::app()->openid = $userinfo['openid'];

                if (!empty($userinfo['unionid'])) {
                    $UnionidInfo = MemberUniqueModel::getUnionidInfo($uniacid, $userinfo['unionid'])->first();

                    if (!empty($UnionidInfo)) {
                        $UnionidInfo = $UnionidInfo->toArray();
                    }
                }  else {
                    $UnionidInfo = [];
                }

                $fans_mode = McMappingFansModel::getUId($userinfo['openid']);

                if ($fans_mode) {
                    $member_shop_info_model = MemberShopInfo::getMemberShopInfo($fans_mode->uid);
                }

                \Log::debug('粉丝', $fans_mode->uid);

                if ((!empty($fans_mode) && !empty($member_shop_info_model))
                        || !empty($UnionidInfo['unionid'])) {

                    \Log::debug('微信登陆更新');

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
                    } else {
                        $member_id = $fans_mode->uid;
                    }

                    if (MemberShopInfo::isBlack($member_id)) {
                        return show_json(-1, '黑名单用户，请联系管理员');
                    }

                    //更新mc_members
                    $mc_data = array(
                        'nickname' => stripslashes($userinfo['nickname']),
                        'avatar' => $userinfo['headimgurl'],
                        'gender' => $userinfo['sex'],
                        'nationality' => $userinfo['country'],
                        'resideprovince' => $userinfo['province'] . '省',
                        'residecity' => $userinfo['city'] . '市'
                    );
                    MemberModel::updataData($UnionidInfo['member_id'], $mc_data);

                    //更新mapping_fans
                    $record = array(
                        'openid' => $userinfo['openid'],
                        'nickname' => stripslashes($userinfo['nickname']),
                        'tag' => base64_encode(serialize($userinfo))
                    );
                    McMappingFansModel::updateData($UnionidInfo['member_id'], $record);
                } else {
                    \Log::debug('添加新会员', $fans_mode->uid);

                    if ($fans_mode->uid) {
                        //更新mc_members
                        $mc_data = array(
                            'nickname' => stripslashes($userinfo['nickname']),
                            'avatar' => $userinfo['headimgurl'],
                            'gender' => $userinfo['sex'],
                            'nationality' => $userinfo['country'],
                            'resideprovince' => $userinfo['province'] . '省',
                            'residecity' => $userinfo['city'] . '市'
                        );
                        MemberModel::updataData($UnionidInfo['member_id'], $mc_data);

                        $member_id = $fans_mode->uid;

                        //更新mapping_fans
                        $record = array(
                            'openid' => $userinfo['openid'],
                            'nickname' => stripslashes($userinfo['nickname']),
                            'tag' => base64_encode(serialize($userinfo))
                        );
                        McMappingFansModel::updateData($UnionidInfo['member_id'], $record);
                    } else {
                        //添加mc_members表
                        $default_group = McGroupsModel::getDefaultGroupId();
                        $uid = MemberModel::insertData($userinfo, array(
                            'uniacid' => $uniacid,
                            'groupid' => $default_group->groupid
                        ));

                        if ($uid !== false) {
                            $member_id = $uid;
                        } else {
                            return show_json(8, '保存用户信息失败');
                        }

                        //添加mapping_fans表
                        McMappingFansModel::insertData($userinfo, array(
                            'uid' => $member_id,
                            'acid' => $uniacid,
                            'uniacid' => $uniacid,
                            'salt' => Client::random(8),
                        ));
                    }

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

                    if (!empty($userinfo['unionid'])) {
                        //添加ims_yz_member_unique表
                        MemberUniqueModel::insertData(array(
                            'uniacid' => $uniacid,
                            'unionid' => $userinfo['unionid'],
                            'member_id' => $member_id,
                            'type' => self::LOGIN_TYPE
                        ));
                    }

                    //触发会员成为下线事件
                    Member::chkAgent($member_id);
                }
                \Log::debug('uid', $member_id);
                Session::set('member_id', $member_id);
            }
        } else {
            $this->_setClientRequestUrl();

            redirect($authurl)->send();
            exit;
        }

        if (empty($params) || !empty($params) && $params['scope'] != 'user_info') {
            \Log::debug('微信登陆成功跳转地址',$redirect_url);
            redirect($redirect_url)->send();
        }
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
