<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/10/30
 * Time: 下午4:20
 */

namespace app\common\listeners\member;


use app\common\events\member\MemberCreateRelationEvent;
use app\common\services\member\MemberRelation;

class MemberCreateRelationEventListener
{
    public function handle(MemberCreateRelationEvent $event)
    {
        $member_id = $event->getUid();
        $parent_id = $event->getParentId();
        \Log::info('创建会员关系');

        $member_relation = new MemberRelation();

        $relation = $member_relation->hasRelationOfParent($member_id, $parent_id, 1);

        if (empty($relation)) {
            $member_relation->addMemberOfRelation($member_id, $parent_id);
        }

        if (!empty($relation) && $parent_id != $relation->parent_id) {
            $member_relation->changeMemberOfRelation($member_id, $parent_id);
        }
    }
}