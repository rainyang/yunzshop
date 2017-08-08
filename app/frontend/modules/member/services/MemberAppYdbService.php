<?php
/**
 * Created by PhpStorm.
 * User: yangming
 * Date: 17/8/2
 * Time: 上午11:20
 */

namespace app\frontend\modules\member\services;

use app\common\helpers\Url;
use app\common\models\Member;
use app\common\services\Session;
use app\frontend\modules\member\models\MemberWechatModel;
use app\frontend\modules\member\services\MemberService;
use app\frontend\modules\member\models\MemberUniqueModel;
use app\frontend\modules\member\models\MemberModel;

class MemberAppYdbService extends MemberService
{
    const LOGIN_TYPE    = 7;

    public function __construct()
    {}

    public function login()
    {
        load()->func('communication');
        $uniacid = \YunShop::app()->uniacid;

        $set = \Setting::get('shop_app.pay');
        $appid = $set['appid'];
        $secret = $set['secret'];

        $para = \YunShop::request();
        if (empty($para['openid'])) {
            return show_json(0, array('msg'=>'请求错误'));
        }
        $member = MemberWechatModel::getUserInfo($para['openid']);
        if (!empty($member) && $_GET) {
            Session::set('member_id', $member['member_id']);
            $url = Url::absoluteApp('home', ["ssid" => $member['member_id']]);
            redirect($url)->send();
            exit();
        }
        //通过接口获取用户信息
        $url ='https://api.weixin.qq.com/sns/userinfo?access_token=' . $para['token'] . '&openid=' . $para['openid'];
        $res = @ihttp_get($url);
        $user_info = json_decode($res['content'], true);
        \Log::info('获取用户信息：' . print_r($user_info, true));
        if (!empty($user_info) && !empty($user_info['unionid'])) {
            //Login
            $member_id = $this->memberLogin($user_info);
            //修改添加yz_member_app_wechat表
            if (!empty(member)) {
                MemberWechatModel::updateUserInfo($user_info['openid'],array(
                    'nickname' => $user_info['nickname'],
                    'avatar' => $user_info['headimgurl'],
                    'gender' => $user_info['sex'],
                ));
            } else {
                MemberWechatModel::insertData(array(
                    'uniacid' => $uniacid,
                    'member_id' => $member_id,
                    'openid' => $user_info['openid'],
                    'nickname' => $user_info['nickname'],
                    'avatar' => $user_info['headimgurl'],
                    'gender' => $user_info['sex'],
                    'nationality' => $user_info['country'],
                    'resideprovince' => $user_info['province'] . '省',
                    'residecity' => $user_info['city'] . '市'
                ));
            }
                $this->createMiniMember($json_user, ['uniacid'=>$uniacid, 'member_id'=>$member_id]);
        } else {
            \Log::info('云打包获取用户信息错误：' . print_r($res, true));
        }
    }
}