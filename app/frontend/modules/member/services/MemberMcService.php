<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/2/22
 * Time: 下午4:12
 */

namespace app\frontend\modules\member\services;

use app\frontend\modules\member\models\MemberModel;

class MemberMcService
{
    public function login()
    {
        if ($this->isLogged()) {
            show_json(1, array('member_id'=> $_SESSION['member_id']));
        }

        $memberdata= \YunShop::request()->memberdata;

        $mobile   = $memberdata['mobile'];
        $password = $memberdata['password'];
        $uniacid  = \YunShop::app()->uniacid;

        if (SZ_YI_DEBUG) {
            $mobile   = '15046101656';
            $password = '123456';
        }

        $has_mobile = MemberModel::checkMobile($uniacid, $mobile);

        if (!empty($has_mobile)) {
            $password = md5($password. $has_mobile['salt'] . \YunShop::app()->config['setting']['authkey']);

            $member_info = MemberModel::getUserInfo($uniacid, $mobile, $password);
        } else {
            return show_json(0, "用户名不存在！");
        }

        if(isMobile()){
            $preUrl = $_COOKIE['preUrl'] ? $_COOKIE['preUrl'] :  Url::app('member.index');
        }else{
            $preUrl = $_COOKIE['preUrl'] ? $_COOKIE['preUrl'] : Url::app('order.index');
        }

        if($member_info){
            if (is_app()) {
                $lifeTime = 24 * 3600 * 3 * 100;
            } else {
                $lifeTime = 24 * 3600 * 3;
            }
            session_set_cookie_params($lifeTime);

            $cookieid = "__cookie_sz_yi_userid_{$uniacid}";

            if (is_app()) {
                setcookie($cookieid, base64_encode($member_info['uid']), time()+3600*24*7);
            } else {
                setcookie($cookieid, base64_encode($member_info['uid']));
            }

            setcookie('member_mobile', $member_info['mobile']);

            if(!isMobile()){
                $openid = base64_decode($_COOKIE[$cookieid]);
                $member_name = !empty($member_info['realname']) ? $member_info['realname'] : $member_info['nickname'];
                $member_name = !empty($member_name) ? $member_name : "未知";
                setcookie('member_name', base64_encode($member_name));
            }

            if (is_app()) {
                return show_json(1, array(
                    'preurl' => $preUrl,
                    'member_id' => $member_info['uid'],
                ));
            } else {
                return show_json(1, array(
                    'preurl' => $preUrl
                ));
            }
        } else{
            return show_json(0, "用户名或密码错误！");
        }
    }

    public function isLogged()
    {
        return !empty($_SESSION['member_id']);
    }

}