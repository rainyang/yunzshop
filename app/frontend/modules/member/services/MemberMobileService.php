<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/2/22
 * Time: 下午4:12
 */

namespace app\frontend\modules\member\services;

use app\frontend\modules\member\services\MemberService;
use app\frontend\modules\member\models\MemberModel;

class MemberMobileService extends MemberService
{
    public function login()
    {
        $memberdata= \YunShop::request()->memberdata;
        $mobile   = $memberdata['mobile'];
        $password = $memberdata['password'];

        $uniacid  = \YunShop::app()->uniacid;

        if ($_SERVER['REQUEST_METHOD'] == 'POST'
                                  && MemberService::validate($mobile, $password)) {
            $has_mobile = MemberModel::checkMobile($uniacid, $mobile);

            if (!empty($has_mobile)) {
                $password = md5($password. $has_mobile['salt']);

                $member_info = MemberModel::getUserInfo($uniacid, $mobile, $password)->first();

            } else {
                return show_json(0, "用户不存在");
            }

            if(!empty($member_info)){
                $member_info = $member_info->toArray();

                $this->save($member_info, $uniacid);

                return show_json(1, array(
                    'member_id' => $member_info['uid'],
                ));
            } else{
                return show_json(0, "手机号或密码错误");
            }
        } else {
            return show_json(-1, "手机号或密码错误");
        }

    }


}