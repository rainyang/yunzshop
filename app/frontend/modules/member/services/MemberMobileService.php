<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/2/22
 * Time: 下午4:12
 */

namespace app\frontend\modules\member\services;

use app\frontend\modules\member\services\MemberService;
use Illuminate\Support\Facades\Cookie;
use app\frontend\modules\member\models\MemberModel;
use Illuminate\Session\Store;

class MemberMobileService extends MemberService
{
    public function login()
    {
        $memberdata= \YunShop::request()->memberdata;
        $mobile   = $memberdata['mobile'];
        $password = $memberdata['password'];

        $uniacid  = \YunShop::app()->uniacid;

        if ((\YunShop::app()->isajax) && (\YunShop::app()->ispost
                                  && MemberService::validate($mobile, $password))) {
            $has_mobile = MemberModel::checkMobile($uniacid, $mobile);

            if (!empty($has_mobile)) {
                $password = md5($password. $has_mobile['salt'] . \YunShop::app()->config['setting']['authkey']);

                $member_info = MemberModel::getUserInfo($uniacid, $mobile, $password);
            } else {
                return show_json(0, "用户名不存在！");
            }

            if($member_info){
                $cookieid = "__cookie_sz_yi_userid_{$uniacid}";

                if (is_app()) {
                    Cookie::queue($cookieid, $member_info['uid'], time()+3600*24*7);
                } else {
                    Cookie::queue($cookieid, $member_info['uid']);
                }

                Cookie::queue('member_mobile', $member_info['uid']);

                if(!isMobile()){
                    $member_name = !empty($member_info['realname']) ? $member_info['realname'] : $member_info['nickname'];
                    $member_name = !empty($member_name) ? $member_name : "未知";
                    session()->put('member_id',$member_info['uid']);
                    session()->put('member_name',$member_name);
                }

                if (is_app()) {
                    return show_json(1, array(
                        'member_id' => $member_info['uid'],
                    ));
                } else {
                    return show_json(1);
                }
            } else{
                return show_json(0, "用户名或密码错误！");
            }
        }

    }
}