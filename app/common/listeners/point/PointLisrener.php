<?php

/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/4/11
 * Time: 下午3:57
 */
namespace app\common\listeners\point;

use app\common\events\order\AfterOrderCreatedEvent;
use app\common\models\Order;
use app\common\services\finance\CalculationPointService;
use app\common\services\finance\PointService;
use app\frontend\modules\finance\services\AfterOrderDeductiblePointService;
use Setting;

class PointLisrener
{
    private $point_set;
    private $order_model;

    public function changePoint(AfterOrderCreatedEvent $event)
    {
        $this->point_set = Setting::get('point.set');
        $this->order_model = Order::find($event->getOrderModel()->id);
        $this->byGoodsGivePoint();
        $this->orderGivePoint();
    }

    private function getPointDataByGoods($order_goods_model)
    {
        $point_data = [
            'point_income_type' => 1,
            'member_id' => $this->order_model->uid,
            'order_id' => $this->order_model->id,
            'point_mode' => 1
        ];
        $point_data += CalculationPointService::calcuationPointByGoods($order_goods_model);
        return $point_data;
    }

    private function getPointDateByOrder()
    {
        $point_data = [
            'point_income_type' => 1,
            'member_id' => $this->order_model->uid,
            'order_id' => $this->order_model->id,
            'point_mode' => 2
        ];
        $point_data += CalculationPointService::calcuationPointByOrder($this->order_model);
        return $point_data;
    }

    private function addPointLog($point_data)
    {
        $this->verifyData($point_data);
        if ($point_data['point']) {
            $point_service = new PointService($point_data);
            $point_model = $point_service->changePoint();
            if ($point_model) {
                //通知成功
            }
        }
    }

    private function verifyData($point_data)
    {
        if (!array_key_exists('point',$point_data)) {
            return;
        }
    }

    private function byGoodsGivePoint()
    {
        foreach ($this->order_model->hasManyOrderGoods as $order_goods_model) {
            $point_data = $this->getPointDataByGoods($order_goods_model);
            $this->addPointLog($point_data);
        }
    }

    private function orderGivePoint()
    {
        $point_data = $this->getPointDateByOrder();
        $this->addPointLog($point_data);
    }

    public function subscribe($events)
    {
        //下单之后 根据商品和订单赠送积分
        $events->listen(
            AfterOrderCreatedEvent::class,
            PointLisrener::class . '@changePoint'
        );

        //下单之后 扣除积分抵扣使用的积分
        $events->listen(
            AfterOrderCreatedEvent::class,
            AfterOrderDeductiblePointService::class . '@deductiblePoint'
        );
    }
}