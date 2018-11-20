<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/10/22
 * Time: 上午11:52
 */

namespace app\common\services\member;


use app\backend\modules\member\models\Member;
use app\backend\modules\member\models\MemberShopInfo;
use app\common\models\member\ChildrenOfMember;
use app\common\models\member\ParentOfMember;
use Illuminate\Support\Facades\DB;

class MemberRelation
{
    public $parent;
    public $child;
    public $map_relaton = [];
    public $map_parent = [];
    public $map_parent_total = 0;

    public function __construct($uid, $parent_id)
    {
        $this->parent = new ParentOfMember();
        $this->child  = new ChildrenOfMember();

        $this->_init($uid, $parent_id);
    }

    private function _init($uid, $parent_id)
    {

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
          $this->child->getMemberByDepth($uid, $depth);
    }

    /**
     * 添加会员关系
     *
     */
    public function addMemberOfRelation($uid, $parent_id)
    {
        DB::transaction(function() use ($uid, $parent_id) {
            $this->parent->addNewParentData($uid, $parent_id);

            $this->child-> addNewChildData($this->parent, $uid, $parent_id);
        });
    }

    /**
     * 删除会员关系
     *
     * @param $uid
     * @throws \Exception
     * @throws \Throwable
     */
    public function delMemberOfRelation($uid)
    {
        DB::transaction(function() use ($uid) {
            $this->child->delMemberOfRelation($this->parent, $uid);

            $this->parent->delMemberOfRelation($this->child, $uid);
        });
    }

    /**
     * 修改后重新添加
     *
     * @param $uid
     * @param $n_parent_id
     */
    public function reAddMemberOfRelation($uid, $n_parent_id)
    {
        DB::transaction(function() use ($uid) {
            //$reData = array_shift($this->map_relaton);

            $this->map_parent_total = count($this->map_parent);

            foreach ($this->map_relaton as $reData) {
                if (!in_array($reData[0], $this->map_parent)) {
                    $this->map_parent[] = $reData[0];

                    $this->map_parent_total = count($this->map_parent);
                }

                foreach ($this->map_parent as $k => $p) {
                    $parent_attr[] = [
                        'uniacid'   => \YunShop::app()->uniacid,
                        'parent_id'  => $p,
                        'level'     => $this->map_parent_total - $k,
                        'member_id' => $reData[1],
                        'created_at' => time()
                    ];


                    $child_attr[] = [
                        'uniacid'   => \YunShop::app()->uniacid,
                        'child_id'  => $reData[1],
                        'level'     => $this->map_parent_total - $k,
                        'member_id' => $p,
                        'created_at' => time()
                    ];

                    //       dd($child_attr);
                }
            }
    //        dd($child_attr);
            $this->child->CreateData($child_attr);
            $this->parent->CreateData($parent_attr);
        });
    }

    /**
     * 修改会员关系
     *
     * @param $uid
     * @param $o_parent_id
     * @param $n_parent_id
     * @throws \Exception
     * @throws \Throwable
     */
    public function changeMemberOfRelation($uid, $o_parent_id, $n_parent_id)
    {
        DB::transaction(function() use ($uid, $o_parent_id, $n_parent_id) {
            $this->delMemberOfRelation($uid, $o_parent_id);

            $this->reAddMemberOfRelation($uid, $n_parent_id);
        });
    }

    public function hasRelationOfParent($uid, $depth)
    {
        return $this->parent->hasRelationOfParent($uid, $depth);
    }

    public function hasRelationOfChild($uid)
    {
        return $this->child->getChildOfMember($uid);
    }

    public function build($member_id, $parent_id)
    {
        $parent_relation = $this->hasRelationOfParent($member_id, 1);
        $child_relation = $this->hasRelationOfChild($member_id);
//dd($child_relation);
        $this->map_relaton[] = [$parent_id, $member_id];

        if (!$parent_relation->isEmpty() && !$child_relation->isEmpty()) {
            foreach ($child_relation as $rows) {
              //  dd($rows->child);
                $ids[] = $rows['child_id'];
            }
//dd($ids);
            $memberInfo = MemberShopInfo::getParentOfMember($ids);
//dd($ids, $memberInfo);
            foreach ($memberInfo as $val) {
                $this->map_relaton[] = [$val['parent_id'], $val['member_id']];
            }

            $parentInfo = $this->parent->getParentsOfMember($parent_id);
            $parentTotal = count($parentInfo);

            foreach ($parentInfo as $rows) {
                $this->map_parent[$parentTotal - $rows['level']] =$rows['parent_id'];
            }

            ksort($this->map_parent);
        }
//dd($this->map_relaton);
        if ($parent_relation->isEmpty() && $child_relation->isEmpty()) {
            $this->addMemberOfRelation($member_id, $parent_id);
        }

        if (!$parent_relation->isEmpty() && $parent_id != $parent_relation[0]->parent_id) {
            $this->changeMemberOfRelation($member_id, $parent_relation[0]->parent_id, $parent_id);
        }
    }
}