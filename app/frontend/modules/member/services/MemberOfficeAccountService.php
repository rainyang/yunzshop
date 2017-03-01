<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/2/22
 * Time: 下午4:44
 */

namespace app\frontend\modules\member\services;

use app\frontend\modules\member\services\MemberMcService;
use app\frontend\modules\member\models\McMappingFansModel;
use app\frontend\modules\member\models\MemberUniqueModel;

class MemberOfficeAccountService extends MemberMcService
{
    private $_login_type    = 1;

    public function __construct()
    {}

    public function login()
    {
        if ($this->isLogged()) {
            show_json(1, array('member_id'=> $_SESSION['member_id']));
        }

        $uniacid      = \YunShop::app()->uniacid;
        $code         = \YunShop::request()->code;

        $appId        = \YunShop::app()->account['key'];
        $appSecret    = \YunShop::app()->account['secret'];

        $callback     = \YunShop::app()->siteroot . 'app/index.php?' . $_SERVER['QUERY_STRING'];

        $authurl = $this->_getAuthUrl($appId, $callback);
        $tokenurl = $this->_getTokenUrl($appId, $appSecret, $code);

        if (!empty($code)) {
            $resp     = @ihttp_get($tokenurl);
            $token    = @json_decode($resp['content'], true);

            if (!empty($token) && !empty($token['errmsg']) && $token['errmsg'] == 'invalid code') {
                show_json(0, array('msg'=>'请求错误'));
            }

            $userinfo_url = $this->_getUserInfoUrl($token['access_token'], $token['openid']);
            $resp_info = @ihttp_get($userinfo_url);
            $userinfo    = @json_decode($resp_info['content'], true);

            if (is_array($userinfo) && !empty($userinfo['unionid'])) {
                $UnionidInfo = MemberUniqueModel::getUnionidInfo($uniacid, $userinfo['unionid']);

                if (!empty($UnionidInfo['unionid'])) {
                    $types = expload($UnionidInfo['type'], '|');
                    $member_id = $UnionidInfo['member_id'];;

                    if (!in_array($this->_login_type, $types)) {
                        //更新ims_yz_member_unique表
                        MemberUniqueModel::updateData(array(
                            'unque_id'=>$UnionidInfo['unque_id'],
                            'type' => $UnionidInfo['type'] . '|' . $this->_login_type
                        ));
                    }
                } else {
                    $member_info = McMappingFansModel::getUId($uniacid, $userinfo['openid']);
                    $member_id = $member_info['uid'];

                    //添加ims_yz_member_unique表
                    MemberUniqueModel::insertData(array(
                        'uniacid' => $uniacid,
                        'unionid' => $userinfo['unionid'],
                        'member_id' => $member_id,
                        'type' => $this->_login_type
                    ));
                }

                $_SESSION['member_id'] = $member_id;
            } else {
                show_json(0, array('url'=> $authurl));
            }
        } else {
            if (SZ_YI_DEBUG) {
                header('location:' . $authurl);exit;
            }
            show_json(0, array('url'=> $authurl));
        }

        show_json(1, array('member_id', $_SESSION['member_id']));
    }

    /**
     * 是否登录
     *
     * @return bool
     */
    public function isLogged()
    {
        return !empty($_SESSION['member_id']);
    }

    /**
     * 授权 api
     *
     * snsapi_base/snsapi_userinfo
     *
     * @param $appId
     * @param $url
     * @return string
     */
    private function _getAuthUrl($appId, $url)
    {
       return "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $appId . "&redirect_uri=" . urlencode($url) . "&response_type=code&scope=snsapi_base&state=123#wechat_redirect";
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
}