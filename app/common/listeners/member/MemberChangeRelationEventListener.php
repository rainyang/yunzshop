<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/11/20
 * Time: 上午7:33
 */

namespace app\common\listeners\member;


use app\common\events\member\MemberChangeRelationEvent;
use app\common\services\member\MemberRelation;

class MemberChangeRelationEventListener
{
    public function handle(MemberChangeRelationEvent $event)
    {
        $member_id = $event->getUid();
        $parent_id = $event->getParentId();

        if (intval($member_id) > 0 && intval($parent_id) > 0) {
            \Log::info('修改会员关系');
            $member_relation = new MemberRelation();

            $parent_relation = $member_relation->hasRelationOfParent($member_id, 1);

            if ($parent_relation->isEmpty()) {

            }

            $member_relation->changebuild($member_id, $parent_id);
        }
    }
}