<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/11
 * Time: 下午3:57
 */

namespace app\common\listeners\point;

use app\common\events\order\AfterOrderCanceledEvent;
use app\common\events\order\AfterOrderCreatedEvent;
use app\common\events\order\AfterOrderReceivedEvent;
use app\common\models\Order;
use app\common\models\UniAccount;
use app\common\services\finance\CalculationPointService;
use app\common\services\finance\PointRollbackService;
use app\common\services\finance\PointService;
use app\frontend\modules\finance\services\AfterOrderDeductiblePointService;
use app\Jobs\PointToLoveJob;
use Setting;

class PointListener
{
    private $pointSet;
    private $orderModel;

    public function changePoint(AfterOrderReceivedEvent $event)
    {
        $this->orderModel = Order::find($event->getOrderModel()->id);
        $this->pointSet = $this->orderModel->getSetting('point.set');
        $this->byGoodsGivePoint();
        $this->orderGivePoint();
    }

    private function getPointDataByGoods($order_goods_model)
    {
        $pointData = [
            'point_income_type' => 1,
            'member_id' => $this->orderModel->uid,
            'order_id' => $this->orderModel->id,
            'point_mode' => 1
        ];
        $pointData += CalculationPointService::calcuationPointByGoods($order_goods_model);
        return $pointData;
    }

    private function getPointDateByOrder()
    {
        $pointData = [
            'point_income_type' => 1,
            'member_id' => $this->orderModel->uid,
            'order_id' => $this->orderModel->id,
            'point_mode' => 2
        ];
        $pointData += CalculationPointService::calcuationPointByOrder($this->orderModel);
        return $pointData;
    }

    private function addPointLog($pointData)
    {
        if (isset($pointData['point'])) {
            $pointService = new PointService($pointData);
            $pointService->changePoint();
        }
    }

    private function byGoodsGivePoint()
    {
        foreach ($this->orderModel->hasManyOrderGoods as $aOrderGoods) {
            $point_data = $this->getPointDataByGoods($aOrderGoods);
            $this->addPointLog($point_data);
        }
    }

    private function orderGivePoint()
    {
        $pointData = $this->getPointDateByOrder();
        $this->addPointLog($pointData);
    }

    public function subscribe($events)
    {
        //收货之后 根据商品和订单赠送积分
        $events->listen(
            AfterOrderReceivedEvent::class,
            PointListener::class . '@changePoint'
        );

        //下单之后 扣除积分抵扣使用的积分
        $events->listen(
            AfterOrderCreatedEvent::class,
            AfterOrderDeductiblePointService::class . '@deductiblePoint'
        );

        //订单关闭 积分抵扣回滚
        $events->listen(
            AfterOrderCanceledEvent::class,
            PointRollbackService::class . '@orderCancel'
        );

        //积分自动转入爱心值
        $events->listen('cron.collectJobs', function() {

            \Log::info("--积分自动转入爱心值检测--");
            $uniAccount = UniAccount::get();
            foreach ($uniAccount as $u) {
                \YunShop::app()->uniacid = $u->uniacid;
                \Setting::$uniqueAccountId = $uniacid = $u->uniacid;

                $point_set = Setting::get('point.set');

                if (isset($point_set['transfer_love'])
                    && $point_set['transfer_love'] == 1
                    && \YunShop::plugin()->get('love')
                    //&& Setting::get('point.last_to_love_time') != date('d')
                    //&& date('H') == 1
                ) {

                    \Log::info("--积分自动转入爱心值Uniacid:{$u->uniacid}加入队列--");
                    \Cron::add("Point_To_Love{$u->uniacid}", '*/30 * * * * *', function () use($uniacid) {
                        (new PointToLoveJob($uniacid))->handle();
                    });
                }
            }
        });
    }
}
