<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/7/9
 * Time: 下午3:09
 */

namespace app\common\listeners\member;


use app\backend\modules\member\models\MemberRelation;
use app\common\events\order\AfterOrderPaidEvent;
use Illuminate\Events\Dispatcher;

class AfterOrderPaidListener
{
    public function handle(AfterOrderPaidEvent $event)
    {
        \Log::debug('AfterOrderPaidEvent');
        $model = $event->getOrderModel();

        \Log::debug('推广资格-' . \YunShop::app()->getMemberId());
        MemberRelation::checkOrderPay($model->uid);
    }

    public function subscribe(Dispatcher $events)
    {
        $res = $events->getListeners(\app\common\events\order\AfterOrderPaidEvent::class);
    }
}