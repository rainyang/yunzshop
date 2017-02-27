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

    public function getUserInfo()
    {
        return  McMappingFansModel::first();
    }

    public function login()
    {
        $uniacid      = \YunShop::app()->uniacid;

        $appId        = \YunShop::app()->account['key'];
        $appSecret    = \YunShop::app()->account['secret'];
        $code         = \YunShop::request()-code;
        $url          = \YunShop::app()->siteroot . 'app/index.php?' . $_SERVER['QUERY_STRING'];

        $authurl = $this->_getAuthUrl($appId, $url);

        $tokenurl = $this->_getTokenUrl($appId, $appSecret, $code);

        if (empty($code)) {
            header('location: ' . $authurl);
            exit();
        } else {
            $resp     = ihttp_get($tokenurl);
            $token    = @json_decode($resp['content'], true);
            if (!empty($token) && is_array($token) && $token['errmsg'] == 'invalid code') {
                header('location: ' . $authurl);
                exit();
            }

            if (is_array($token) && !empty($token['unionid'])) {
                $UnionidInfo = MemberUniqueModel::getUnionidInfo($uniacid, $token['unionid']);

                $types = expload($UnionidInfo['type'], '|');

                if ($UnionidInfo['unionid']) {
                     if (!in_array($this->_login_type, $types)) {
                         //更新ims_yz_member_unique表
                         //添加ims_yz_member_office_account表
                     }
                } else {
                         //添加ims_mc_member表
                         //更新ims_yz_member_unique表
                         //添加ims_yz_member_office_account表
                }
            } else {
                $querys = explode('&', $_SERVER['QUERY_STRING']);
                $newq   = array();

                foreach ($querys as $q) {
                    if (!strexists($q, 'code=') && !strexists($q, 'state=') && !strexists($q, 'from=') && !strexists($q, 'isappinstalled=')) {
                        $newq[] = $q;
                    }
                }

                $rurl    = \YunShop::app()->siteroot . 'app/index.php?' . implode('&', $newq);
                $authurl = $this->_getAuthUrl($appId, $rurl);

                header('location: ' . $authurl);
                exit;
            }


        }
    }

    private function _getAuthUrl($appId, $url)
    {
       return "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $appId . "&redirect_uri=" . urlencode($url) . "&response_type=code&scope=snsapi_base&state=123#wechat_redirect";
    }

    private function _getTokenUrl($appId, $appSecret, $code)
    {
       return "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $appId . "&secret=" . $appSecret . "&code=" . $code . "&grant_type=authorization_code";
    }
}