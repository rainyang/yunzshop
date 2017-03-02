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
use app\frontend\modules\member\models\MemberUniqueModel;
use app\frontend\modules\member\models\MemberModel;

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
        $res = @ihttp_request($url, $data);

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

            if ($UnionidInfo['unionid']) {
                $types = expload($UnionidInfo['type'], '|');
                $member_id = $UnionidInfo['member_id'];

                if (!in_array($this->_login_type, $types)) {
                    //更新ims_yz_member_unique表
                    MemberUniqueModel::updateData(array(
                        'unque_id'=>$UnionidInfo['unque_id'],
                        'type' => $UnionidInfo['type'] . '|' . $this->_login_type
                    ));

                    //添加ims_yz_member_mini_app表
                    MemberMiniAppModel::insertData(array(
                        'uniacid' => $uniacid,
                        'member_id' => $UnionidInfo['member_id'],
                        'openid' => $json_user['openid'],
                        'nickname' => $json_user['nickname'],
                        'avatar' => $json_user['headimgurl'],
                        'gender' => $json_user['sex'],
                        'nationality' => $json_user['country'],
                        'resideprovince' => $json_user['province'] . '省',
                        'residecity' => $json_user['city'] . '市',
                        'created_at' => time()
                    ));
                }
            } else {
                //添加ims_mc_member表
                $member_id = MemberModel::insertData(array(
                    'uniacid' => $uniacid,
                    'groupid' => $json_user['unionid'],
                    'createtime' => TIMESTAMP,
                    'nickname' => $json_user['nickname'],
                    'avatar' => $json_user['headimgurl'],
                    'gender' => $json_user['sex'],
                    'nationality' => $json_user['country'],
                    'resideprovince' => $json_user['province'] . '省',
                    'residecity' => $json_user['city'] . '市'
                ));


                //添加ims_yz_member_unique表
                MemberUniqueModel::insertData(array(
                    'uniacid' => $uniacid,
                    'unionid' => $json_user['unionid'],
                    'member_id' => $member_id,
                    'type' => $this->_login_type
                ));

                //添加ims_yz_member_mini_app表
                MemberMiniAppModel::insertData(array(
                    'uniacid' => $uniacid,
                    'member_id' => $member_id,
                    'openid' => $json_user['openid'],
                    'nickname' => $json_user['nickname'],
                    'avatar' => $json_user['headimgurl'],
                    'gender' => $json_user['sex'],
                    'nationality' => $json_user['country'],
                    'resideprovince' => $json_user['province'] . '省',
                    'residecity' => $json_user['city'] . '市',
                    'created_at' => time()
                ));
            }

            $random = $this->wx_app_session($user_info);

            $result = array('session' => $random, 'wx_token' =>session_id(), 'uid' => $member_id);

            show_json(1, $result);
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
            $this->returnError('登录认证失败！');
        }

        $random = md5(uniqid(mt_rand()));

        $_SESSION['wx_app'] = array($random => iserializer(array('session_key'=>$user_info['session_key'], 'openid'=>$user_info['openid'])));

        return $random;
    }
}