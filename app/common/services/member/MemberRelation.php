<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/10/22
 * Time: 上午11:52
 */

namespace app\common\services\member;


use app\backend\modules\member\models\Member;
use app\common\models\MemberShopInfo;

class MemberRelation
{
    public function createParentOfMember()
    {
        /*        $memberInfo = new Member();

                //$data = $memberInfo->getTreeAllNodes($uniacid);
                //\Log::debug('--------queue data count-----', $data->cout());
                $data = $memberInfo->getDescendants(5, 65);

        dd($data);*/

        $pageSize = 1000;
        $pageSize = 10;

        $member_info = Member::getQueueAllMembersInfo(\YunShop::app()->uniacid);

        $total       = $member_info->count();
        $total_page  = ceil($total/$pageSize);

        \Log::debug('------total-----', $total);
        \Log::debug('------total_page-----', $total_page);

        //Cache::put('queque_wechat_total', $total_page, 30);
        $total_page = 2;
        for ($curr_page = 1; $curr_page <= $total_page; $curr_page++) {
            \Log::debug('------curr_page-----', $curr_page);
            $offset      = ($curr_page - 1) * $pageSize;
            $member_info = Member::getQueueAllMembersInfo(\YunShop::app()->uniacid, $pageSize, $offset)->get();
            \Log::debug('------member_count-----', $member_info->count());

            $job = (new \app\Jobs\memberParentOfMemberJob(\YunShop::app()->uniacid, $member_info));
            dispatch($job);
        }
    }
}