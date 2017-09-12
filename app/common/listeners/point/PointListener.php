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
use app\common\services\finance\CalculationPointService;
use app\common\services\finance\PointRollbackService;
use app\common\services\finance\PointService;
use app\frontend\modules\finance\services\AfterOrderDeductiblePointService;
use Setting;

class PointListener
{
    private $pointSet;
    private $orderModel;

    public function changePoint(AfterOrderReceivedEvent $event)
    {
        $this->pointSet = Setting::get('point.set');
        $this->orderModel = Order::find($event->getOrderModel()->id);
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
    }
}
