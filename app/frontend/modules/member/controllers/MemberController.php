<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/1
 * Time: 下午4:39
 */

namespace app\frontend\modules\member\controllers;

use app\common\components\ApiController;
use app\frontend\modules\member\models\Member;
use app\common\components\BaseController;
use app\frontend\modules\member\models\MemberModel;
use app\common\models\Order;
use app\backend\modules\member\models\MemberRelation;

class MemberController extends ApiController
{

    /**
     * 获取用户信息
     *
     * @return array
     */
    public function getUserInfo()
    {
        $member_id = \YunShop::request()->uid;

        if (!empty($member_id)) {
            $member_info = MemberModel::getUserInfos($member_id)->first();

            if (!empty($member_info)) {
                $member_info = $member_info->toArray();

                if (!empty($member_info['yz_member'])) {
                    if (!empty($member_info['yz_member']['group'])) {
                        $member_info['group_id'] = $member_info['yz_member']['group']['id'];
                        $member_info['group_name'] = $member_info['yz_member']['group']['group_name'];
                    }

                    if (!empty($member_info['yz_member']['level'])) {
                        $member_info['level_id'] = $member_info['yz_member']['level']['id'];
                        $member_info['level_name'] = $member_info['yz_member']['level']['level_name'];
                    }
                }

                $order_info = Order::getOrderCountGroupByStatus([Order::WAIT_PAY,Order::WAIT_SEND,Order::WAIT_RECEIVE,Order::COMPLETE]);

                $member_info['order'] = $order_info;
                return $this->successJson('', $member_info);
            } else {
                return $this->errorJson('用户不存在');
            }

        } else {
            return $this->errorJson('缺少访问参数');
        }

    }

    public function getMemberRelationInfo()
    {
        $info = MemberRelation::getSetInfo()->first()->toJson();

        echo '<pre>';print_r($info);exit;
    }
}