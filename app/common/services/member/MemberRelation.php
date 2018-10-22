<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/10/22
 * Time: 上午11:52
 */

namespace app\common\services\member;


use app\backend\modules\member\models\Member;
use app\common\models\member\ChildenOfMember;
use app\common\models\MemberShopInfo;

class MemberRelation
{
    public function createParentOfMember()
    {
        \Log::debug('------queue start-----');
        $job = (new \app\Jobs\memberParentOfMemberJob(\YunShop::app()->uniacid));
        dispatch($job);

        /*$pageSize = 1000;
        $member_info = Member::getAllMembersInfosByQueue(\YunShop::app()->uniacid);

        $total       = $member_info->count();
        $total_page  = ceil($total/$pageSize);

        \Log::debug('------total-----', $total);
        \Log::debug('------total_page-----', $total_page);*/

        //Cache::put('queque_wechat_total', $total_page, 30);
        /*$member_model = new Member();
        $childMemberModel = new ChildenOfMember();*/

        /*for ($curr_page = 1; $curr_page <= $total_page; $curr_page++) {
            \Log::debug('------curr_page-----', $curr_page);
            $offset      = ($curr_page - 1) * $pageSize;
            $member_info = Member::getAllMembersInfosByQueue(\YunShop::app()->uniacid, $pageSize, $offset)->get();
            \Log::debug('------member_count-----', $member_info->count());

            //$job = (new \app\Jobs\memberParentOfMemberJob(\YunShop::app()->uniacid, $member_model, $childMemberModel, $member_info));
            $job = (new \app\Jobs\memberParentOfMemberJob(\YunShop::app()->uniacid, $member_info));
            dispatch($job);
        }*/
    }
}