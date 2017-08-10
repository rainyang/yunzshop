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
use app\frontend\models\MemberShopInfo;
use app\frontend\modules\member\models\McMappingFansModel;
use app\frontend\modules\member\models\MemberWechatModel;
use app\frontend\modules\member\services\MemberService;
use app\frontend\modules\member\models\MemberUniqueModel;
use app\frontend\modules\member\models\MemberModel;

class MemberAppYdbService extends MemberService
{
    const LOGIN_TYPE    = 7;

    public function __construct()
    {

    }

    public function login()
    {
        load()->func('communication');
        $uniacid = \YunShop::app()->uniacid;
        $mobile   = \YunShop::request()->mobile;
        $password = \YunShop::request()->password;
        if (!empty($mobile) && !empty($password)){
            if (\Request::isMethod('post') && MemberService::validate($mobile, $password)) {
                $has_mobile = MemberModel::checkMobile($uniacid, $mobile);

                if (!empty($has_mobile)) {
                    $password = md5($password. $has_mobile->salt);

                    $member_info = MemberModel::getUserInfo($uniacid, $mobile, $password)->first();

                } else {
                    return show_json(7, "用户不存在");
                }

                if(!empty($member_info)){
                    $member_info = $member_info->toArray();

                    //生成分销关系链
                    Member::createRealtion($member_info['uid']);

                    $this->save($member_info, $uniacid);

                    $yz_member = MemberShopInfo::getMemberShopInfo($member_info['uid']);

                    if (!empty($yz_member)) {
                        $yz_member = $yz_member->toArray();

                        $data = MemberModel::userData($member_info, $yz_member);
                    } else {
                        $data = $member_info;
                    }

                    return show_json(1, $data);
                } {
                    return show_json(6, "手机号或密码错误");
                }
            } else {
                return show_json(6, "手机号或密码错误");
            }
        } else {
            $set = \Setting::get('shop_app.pay');
            $appid = $set['appid'];
            $secret = $set['secret'];

            $para = \YunShop::request();
            if (empty($para['openid'])) {
                return show_json(0, array('msg' => '请求错误'));
            }
            $member = MemberWechatModel::getUserInfo($para['openid']);
            if (!empty($member) && $_GET) {
                Session::set('member_id', $member['member_id']);
                $url = Url::absoluteApp('home', ["ssid" => $member['member_id']]);
                redirect($url)->send();
                exit();
            }
            //通过接口获取用户信息
            $url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $para['token'] . '&openid=' . $para['openid'];
            $res = @ihttp_get($url);
            $user_info = json_decode($res['content'], true);
            \Log::info('获取用户信息：' . print_r($user_info, true));
            unset($user_info['province']);
            if (!empty($user_info) && !empty($user_info['unionid'])) {
                //Login
                $member_id = $this->memberLogin($user_info);
                //添加yz_member_app_wechat表
                MemberWechatModel::insertData(array(
                    'uniacid' => $uniacid,
                    'member_id' => $member_id,
                    'openid' => $user_info['openid'],
                    'nickname' => $user_info['nickname'],
                    'avatar' => $user_info['headimgurl'],
                    'gender' => $user_info['sex'],
                    'province' => '',
                    'country' => '',
                    'city' => ''
                ));
                //$this->createMiniMember($user_info, ['uniacid' => $uniacid, 'member_id' => $member_id]);
            } else {
                \Log::info('云打包获取用户信息错误：' . print_r($res, true));
            }
        }
    }

    public function getFansModel($openid)
    {
        return McMappingFansModel::getUId($openid);
    }
}