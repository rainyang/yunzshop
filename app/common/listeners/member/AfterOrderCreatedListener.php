<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2017/4/8
 * Time: 下午1:59
 */

namespace app\common\listeners\member;

use app\backend\modules\member\models\MemberRelation;
use app\common\events\order\AfterOrderCreatedEvent;

class AfterOrderCreatedListener
{
    public function handle(AfterOrderCreatedEvent $event)
    {
        //MemberRelation::checkOrderConfirm();
    }
}