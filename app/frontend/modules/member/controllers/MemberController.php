<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/1
 * Time: 下午4:39
 */

namespace app\frontend\modules\member\controllers;

use app\backend\modules\member\models\MemberRelation;
use app\common\components\ApiController;
use app\common\models\Order;
use app\frontend\modules\member\models\Member;
use app\frontend\modules\member\models\MemberModel;
use app\frontend\modules\member\models\SubMemberModel;

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

    /**
     * 会员关系链
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMemberRelationInfo()
    {
        $info = MemberRelation::getSetInfo()->first()->toArray();

        $member_info = SubMemberModel::getMemberShopInfo(\YunShop::app()->getMemberId());

        if (empty($info) || empty($member_info)) {
            return $this->errorJson('缺少参数');
        }

        switch ($info['become']) {
           case 2:
               $desc = $info['become_ordercount'];
               break;
           case 3:
               $desc = $info['become_moneycount'];
               break;
           case 4:
               $desc = '指定商品';
               break;
           default:
               $desc = '';
       }

       // TODO 消费和购买指定商品达到条件后 返回审核状态

       $relation = [
           'switch' => $info['status'],
           'become' => $info['become'],
           'desc'   => $desc,
           'is_agent' => $member_info['is_agent'],
           'status' => $member_info['status'],
       ];

        return $this->successJson('', $relation);
    }

    /**
     * 会员是否有推广权限
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function isAgent()
    {
        $member_info = SubMemberModel::getMemberShopInfo(\YunShop::app()->getMemberId());

        return $this->successJson('', ['is_agent' => $member_info['is_agent']]);
    }
}