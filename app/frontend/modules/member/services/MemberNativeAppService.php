<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2019/7/9
 * Time: 上午11:03
 */

namespace app\frontend\modules\member\services;


use app\common\helpers\Client;
use app\common\models\Store;
use app\frontend\models\Member;
use app\frontend\models\MemberShopInfo;
use app\frontend\modules\member\models\MemberModel;
use app\frontend\modules\member\models\SubMemberModel;

class MemberNativeAppService extends MemberService
{
    public function login()
    {
        $mobile   = \YunShop::request()->mobile;
        $password = \YunShop::request()->password;

        $uniacid = \YunShop::app()->uniacid;

        if (\Request::isMethod('post')
            && MemberService::validate($mobile, $password)) {
            $has_mobile = MemberModel::checkMobile($uniacid, $mobile);

            if (!empty($has_mobile)) {
                $password = md5($password . $has_mobile->salt);

                $member_info = MemberModel::getUserInfo($uniacid, $mobile, $password)->first();

            } else {
                return show_json(7, '',"用户不存在");
            }

            if (!empty($member_info)) {
                $member_info = $member_info->toArray();

                $yz_member = MemberShopInfo::getMemberShopInfo($member_info['uid']);

                if (!empty($yz_member)) {
                    $store_member = Store::uniacid()->where('uid', $yz_member->member_id)->count();

                    if (!$store_member) {
                        return show_json(-1,"您不是店长");
                    }

                    //生成分销关系链
                    Member::createRealtion($member_info['uid']);

                    $data['token'] = Client::create_token('yz');
                    $yz_member->access_token_2 = $data['token'];

                    $yz_member->save();
                } else {
                    return show_json(7, '用户不存在');
                }

                return show_json(1, '', $data);
            }
            {
                return show_json(6, '手机号或密码错误');
            }
        } else {
            return show_json(6, '手机号或密码错误');
        }
    }

    /**
     * 验证登录状态
     *
     * @return bool
     */
    public function checkLogged()
    {
        $token = \Yunshop::request()->yz_token;

        if (empty($token)) {
            return false;
        }

        $member = SubMemberModel::getMemberByNativeToken($token);
        \Log::debug('---------native checkLogged--------', [$token, $member->member_id]);
        if (!is_null($member)) {
            return true;
        } else {
            return false;
        }
    }
}