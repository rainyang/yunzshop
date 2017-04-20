<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2017/4/14
 * Time: 下午10:49
 */

namespace app\common\listeners\member;

use app\backend\modules\member\models\MemberRelation;

class AfterOrderReceivedListener
{
    public function handle(AfterOrderReceivedEvent $event)
    {
        MemberRelation::checkOrderFinish();
    }
}