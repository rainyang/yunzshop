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
        if ($this->isLogged()) {
            show_json(1, array('member_id'=> $_SESSION['member_id'], 'url'=>Url::app('account.index')));
        }

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
                         MemberUniqueModel::updateData(array(
                             'unque_id'=>$UnionidInfo['unque_id'],
                             'type' => $UnionidInfo['type'] . '|' . $this->_login_type
                         ));
                     }

                     $_SESSION['member_id'] = $UnionidInfo['member_id'];
                } else {
                        $member_id = McMappingFansModel::getUId($uniacid, $token['openid']);
                         //添加ims_yz_member_unique表
                        MemberUniqueModel::insertData(array(
                            'uniacid' => $uniacid,
                            'unionid' => $token['unionid'],
                            'member_id' => $member_id,
                            'type' => $this->_login_type
                        ));

                        $_SESSION['member_id'] = $member_id;
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

        show_json(1, array('member_id', $_SESSION['member_id']));
    }

    public function isLogged()
    {
        return !empty($_SESSION['member_id']);
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