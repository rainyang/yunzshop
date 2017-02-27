<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/2/23
 * Time: 上午11:20
 */

namespace app\frontend\modules\member\services;

use app\frontend\modules\member\services\MemberMcService;
use app\frontend\modules\member\models\MemberMiniAppModel;

class MemberMiniAppService extends MemberMcService
{
    private $_login_type    = 1;

    public function __construct()
    {}

    public function login()
    {
        include "./addons/sz_yi/core/inc/plugin/vendor/wechat/wxBizDataCrypt.php";
        include "./framework/model/mc.mod.php";

        $uniacid = \YunApp::app()->uniacid;

        session_start();
        load()->func('communication');

        $setdata = pdo_fetch("select * from " . tablename('sz_yi_wxapp') . ' where uniacid=:uniacid limit 1', array(
            ':uniacid' => $uniacid
        ));

        $appid = $setdata['appid'];
        $secret = $setdata['secret'];

        $para = \YunApp::request();

        $data = array(
            'appid' => $appid,
            'secret' => $secret,
            'js_code' => $para['code'],
            'grant_type' => 'authorization_code',
        );

        $url = 'https://api.weixin.qq.com/sns/jscode2session';
        $res = ihttp_request($url, $data);

        $user_info = json_decode($res['content'], true);

        $data = '';  //json

        if (!empty($para['info'])) {
            $json_data = json_decode($para['info'], true);

            $pc = new \WXBizDataCrypt($appid, $user_info['session_key']);
            $errCode = $pc->decryptData($json_data['encryptedData'], $json_data['iv'], $data);
        }

        if ($errCode == 0) {
            $json_user = json_decode($data, true);
        } else {
            $this->returnError('登录认证失败');
        }

        if (!empty($json_user) && !empty($json_user['unionid'])) {
            $UnionidInfo = MemberUniqueModel::getUnionidInfo($uniacid, $json_user['unionid']);

            $types = expload($UnionidInfo['type'], '|');

            if ($UnionidInfo['unionid']) {
                if (!in_array($this->_login_type, $types)) {
                    //更新ims_yz_member_unique表
                    //添加ims_yz_member_mini_app表
                }
            } else {
                //添加ims_mc_member表
                //更新ims_yz_member_unique表
                //添加ims_yz_member_mini_app表
            }
        }
    }
}