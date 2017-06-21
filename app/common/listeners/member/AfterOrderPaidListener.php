<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/20
 * Time: 下午6:44
 */

namespace app\common\listeners\member;


use app\backend\modules\member\models\MemberRelation;

class AfterOrderPiadListener
{
    public function handle(AfterOrderPaidEvent $event)
    {
        \Log::debug('AfterOrderPaidEvent');
        $model = $event->getOrderModel();

        \Log::debug('推广资格-' . \YunShop::app()->getMemberId());
        MemberRelation::checkOrderPay($model->uid);
    }
}