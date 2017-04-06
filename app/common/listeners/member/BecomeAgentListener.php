<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2017/4/6
 * Time: 下午9:55
 */

namespace app\common\listeners;


use app\backend\modules\member\models\MemberRelation;
use app\common\events\BecomeAgent;

class BecomeAgentListener
{
    public function handle(BecomeAgent $event)
    {

        $model = $event->getMemberModel();
        $mid = $event->getMid();

        $relation =MemberRelation();

        $relation->createChildAgent($mid, $model);
    }
}