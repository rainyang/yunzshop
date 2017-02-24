<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/2/22
 * Time: 上午11:56
 */

namespace app\frontend\modules\member\controllers;

use app\common\components\BaseController;
use app\modules\MemberMcModel;

class LoginController extends BaseController
{
    private $error = array();

    public function index()
    {
        //islogined;

        if ((\YunShop::app()->isajax) && (\YunShop::app()->ispost && $this->_validate())) {
            $memberdata= \YunShop::request()->memberdata;

            $mobile   = $memberdata['mobile'];
            $password = $memberdata['password'];
            $uniacid  = \YunShop::app()->uniacid;

            $info = MemberMcModel::where('uniacid', $uniacid)
                                   ->where('mobile', $mobile)
                                   ->where('password', md5($password))->first;

            if(isMobile()){
                $preUrl = $_COOKIE['preUrl'] ? $_COOKIE['preUrl'] : $this->createMobileUrl('member');
            }else{
                $preUrl = $_COOKIE['preUrl'] ? $_COOKIE['preUrl'] : $this->createMobileUrl('order');
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
                    setcookie($cookieid, base64_encode($info['openid']), time()+3600*24*7);
                } else {
                    setcookie($cookieid, base64_encode($info['openid']));
                }

                setcookie('member_mobile', $info['mobile']);

                if(!isMobile()){
                    $openid = base64_decode($_COOKIE[$cookieid]);
                    $member_info = pdo_fetch('select realname,nickname,mobile from ' . tablename('sz_yi_member') . ' where   uniacid=:uniacid and openid=:openid limit 1', array(
                            ':uniacid' => $uniacid,
                            ':openid' => $openid,
                    ));
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

        include $this->template('member/login');
    }



    private function validate()
    {}
}