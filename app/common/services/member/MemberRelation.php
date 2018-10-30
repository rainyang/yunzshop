<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/10/22
 * Time: 上午11:52
 */

namespace app\common\services\member;


use app\backend\modules\member\models\Member;
use app\common\models\member\ChildrenOfMember;
use app\common\models\member\ParentOfMember;
use Illuminate\Support\Facades\DB;

class MemberRelation
{
    public $parent;
    public $child;

    public function __construct()
    {
        $this->parent = new ParentOfMember();
        $this->child  = new ChildrenOfMember();
    }

    /**
     * 批量统计会员父级
     *
     */
    public function createParentOfMember()
    {
        \Log::debug('------queue parent start-----');
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
        $childMemberModel = new ChildrenOfMember();*/

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

    /**
     * 批量统计会员子级
     *
     */
    public function createChildOfMember()
    {
        \Log::debug('------queue child start-----');
        $job = (new \app\Jobs\memberChildOfMemberJob(\YunShop::app()->uniacid));
        dispatch($job);
    }

    /**
     * 获取会员指定层级的子级
     *
     * @param $uid
     * @param $depth
     * @return mixed
     */
    public function getMemberByDepth($uid, $depth)
    {
        return $this->child->getMemberByDepth($uid, $depth);
    }

    /**
     * 添加会员关系
     *
     */
    public function addMemberOfRelation($uid, $parent_id)
    {
        DB::transaction(function() use ($uid, $parent_id) {
            $this->parent->addNewParentData($uid, $parent_id);

            $this->child->addNewChildData($this->parent, $uid, $parent_id);
        });
    }

    public function delMemberOfRelation($uid)
    {
        DB::transaction(function() use ($uid) {
            $this->parent->delMemberOfRelation($uid);

            $this->child->delMemberOfRelation($this->parent, $uid);
        });
    }

    public function changeMemberOfRelation($uid, $o_parent_id, $n_parent_id)
    {
        DB::transaction(function() use ($uid, $o_parent_id, $n_parent_id) {
            $this->delMemberOfRelation($uid, $o_parent_id);

            $this->addMemberOfRelation($uid, $n_parent_id);
        });
    }
}