<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2017/4/6
 * Time: 下午9:55
 */

namespace app\common\listeners\member;


use app\backend\modules\member\models\MemberRelation;
use app\common\events\BecomeAgent;
use app\frontend\modules\member\models\MemberModel;

class BecomeAgentListener
{
    public function handle(BecomeAgent $event)
    {
        $model = $event->getMemberModel();
        $mid = $event->getMid();

        $relation = new MemberRelation();
        $relation->becomeChildAgent($mid, $model);

        //生成关系3级关系链
        $member_model = MemberModel::getMyAgentsParentInfo($mid)->first();

        if (!empty($member_model)) {
            $member_data = $member_model->toArray();

            $relation_str = $mid;

            if (!empty($member_data['yz_member'])) {
                $count = count($member_data['yz_member'], 1);

                if ($count > 3) {
                    $relation_str .= ',' . $member_data['yz_member']['parent_id'];
                }

                if ($count > 6) {
                    $relation_str .= ',' . $member_data['yz_member']['has_one_pre_self']['parent_id'];
                }

            }

            $agent_data = [
                'member_id' => $model->member_id,
                'parent_id' => $mid
            ];

            $model->relation = $relation_str;

            if ($model->save()) {
                $agent_data['parent'] = $relation_str;
            } else {
                $agent_data['parent'] = 0;
            }

            //触发分销事件
            event(new RegisterByAgent($agent_data));
        }
    }
}