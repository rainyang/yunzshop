<?php
/**
 * Created by PhpStorm.
 * User: yangming
 * Date: 17/8/2
 * Time: 上午11:20
 */

namespace app\frontend\modules\member\services;

use app\common\helpers\Client;
use app\common\helpers\Url;
use app\common\services\Session;
use app\frontend\modules\member\models\McMappingFansModel;
use app\frontend\modules\member\models\MemberWechatModel;
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
            if (!empty($user_info) && !empty($user_info['unionid'])) {
                $this->memberLogin($user_info);
            } else {
                \Log::info('云打包获取用户信息错误：' . print_r($res, true));
            }
        }
    }

    public function updateMemberInfo($member_id, $userinfo)
    {
        parent::updateMemberInfo($member_id, $userinfo);

        $record = array(
            'openid' => $userinfo['openid'],
            'nickname' => stripslashes($userinfo['nickname'])
        );

        MemberWechatModel::updateData($member_id, $record);
    }

    public function addMemberInfo($uniacid, $userinfo)
    {
        $uid = parent::addMemberInfo($uniacid, $userinfo);

        $this->addMcMemberFans($uid, $uniacid, $userinfo);
        $this->addFansMember($uid, $uniacid, $userinfo);

        return $uid;
    }

    public function addMcMemberFans($uid, $uniacid, $userinfo)
    {
        McMappingFansModel::insertData($userinfo, array(
            'uid' => $uid,
            'acid' => $uniacid,
            'uniacid' => $uniacid,
            'salt' => Client::random(8),
        ));
    }

    public function addFansMember($uid, $uniacid, $userinfo)
    {
        MemberWechatModel::insertData(array(
            'uniacid' => $uniacid,
            'member_id' => $uid,
            'openid' => $userinfo['openid'],
            'nickname' => $userinfo['nickname'],
            'avatar' => $userinfo['headimgurl'],
            'gender' => $userinfo['sex'],
            'province' => '',
            'country' => '',
            'city' => ''
        ));
    }

    public function getFansModel($openid)
    {
        return McMappingFansModel::getUId($openid);
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
}