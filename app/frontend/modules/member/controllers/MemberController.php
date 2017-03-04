<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/1
 * Time: 下午4:39
 */

namespace app\frontend\modules\member\controllers;

use app\frontend\modules\member\models\Member;
use app\common\components\BaseController;
use app\frontend\modules\member\models\MemberModel;

class MemberController extends BaseController
{
    public function index()
    {
        dd(Member::getNickNnme());
    }

    /**
     * 获取用户信息
     *
     * @return array
     */
    public function getUserInfo()
    {
        $uniacid = \YunShop::app()->uniacid;
        $member_id = \YunShop::request()->uid;

        if (!empty($member_id)) {
            $member_info = MemberModel::getUserInfos($uniacid, $member_id);

            if (!empty($member_info)) {
                return show_json(1, array($member_info));
            } else {
                return show_json(0, array("msg" => '用户不存在'));
            }

        } else {
            return show_json(0, array("msg" => '缺少member_id参数'));
        }

    }
}