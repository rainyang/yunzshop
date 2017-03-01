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
    private $_login_type    = 5;

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

        $info = MemberModel::where('uniacid', $uniacid)
            ->where('mobile', $mobile)
            ->where('password', md5($password))->first();
echo '<pre>';print_r($info);exit;
        if(isMobile()){
            $preUrl = $_COOKIE['preUrl'] ? $_COOKIE['preUrl'] :  Url::app('member.index');
        }else{
            $preUrl = $_COOKIE['preUrl'] ? $_COOKIE['preUrl'] : Url::app('order.index');
        }

        if($info){
            if (is_app()) {
                $lifeTime = 24 * 3600 * 3 * 100;
            } else {
                $lifeTime = 24 * 3600 * 3;
            }
            session_set_cookie_params($lifeTime);

            $cookieid = "__cookie_sz_yi_userid_{$uniacid}";

            if (is_app()) {
                setcookie($cookieid, base64_encode($info['uid']), time()+3600*24*7);
            } else {
                setcookie($cookieid, base64_encode($info['uid']));
            }

            setcookie('member_mobile', $info['mobile']);

            if(!isMobile()){
                $openid = base64_decode($_COOKIE[$cookieid]);
                $member_info = MemberModel::select(array('realname', 'nickname', 'mobile'))->where('uniacid', $uniacid)->where('mobile', $mobile)->get();

                $member_name = !empty($member_info['realname']) ? $member_info['realname'] : $member_info['nickname'];
                $member_name = !empty($member_name) ? $member_name : "未知";
                setcookie('member_name', base64_encode($member_name));
            }

            if (is_app()) {
                return show_json(1, array(
                    'preurl' => $preUrl,
                    'open_id' => $info['openid'],
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

    public function logout()
    {}

    public function isLogged()
    {
        return !empty($_SESSION['member_id']);
    }

}