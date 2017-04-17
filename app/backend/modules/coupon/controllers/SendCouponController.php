<?php

namespace app\backend\modules\coupon\controllers;

use app\common\components\BaseController;

class SendCouponController extends BaseController
{
    //发放优惠券
    public function index()
    {
        //获取会员等级 $memberLevels  ims_yz_member_level

        //获取会员分组 $groups  ims_yz_member_group

        //获取分销商等级  $agentLevels  ims_yz_agent_level



        //获取操作员的ID

        //获取表单提交的会员 Member ID

        //获取couponId, 更新优惠券的推送设置

        //获取目标的memberId (核对用户是否存在)

        //获取发放的数量 (不小于1)

        //发放到 coupon_member (发送失败时有记录, 并显示)

        //发送模板消息

        //记录到 log todo 日志logno怎么生成




        return view('coupon.send', [
//            'coupon' => $coupon,
        ])->render();
    }
}