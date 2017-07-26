<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/2
 * Time: 上午10:59
 */

namespace app\frontend\modules\finance\listeners;

use app\common\events\discount\OnDeductionInfoDisplayEvent;
use app\common\events\discount\OnDeductionPriceCalculatedEvent;
use app\frontend\models\order\PreOrderDeduction;
use app\frontend\modules\finance\services\AfterOrderDeductiblePointService;
use app\frontend\modules\finance\services\CalculationPointService;

class Order
{
    protected $event;
    protected $deductionId = 1;

    public function onDisplay(OnDeductionInfoDisplayEvent $event)
    {
        $this->event = $event;

        $data = $this->getPointData();
        if (!$data) {
            return null;
        }
        $event->addData($data);
    }

    protected function isChecked()
    {
        $deduction_ids = $this->event->getOrderModel()->getParams('deduction_ids');

        return AfterOrderDeductiblePointService::isChecked($deduction_ids, $this->deductionId);
    }

    protected function getPointData()
    {
        $orderModel = $this->event->getOrderModel();

        $point = new CalculationPointService($orderModel->getOrderGoodsModels(), $orderModel->uid);

        if ($point == false || empty($point->point)) {
            return false;
        }
        $data = [
            'id' => '1',//抵扣表id
            'name' => '积分抵扣',//名称
            'value' => $point->point,//数值
            'price' => $point->point_money,//金额
            'checked' => $this->isChecked(),//是否选中
        ];
        return $data;
    }

    public function onCalculated(OnDeductionPriceCalculatedEvent $event)
    {
        $this->event = $event;

        if ($this->isChecked() == false) {
            return null;
        }
        $data = $this->getPointData();
        if (!$data) {
            return null;
        }
        $attributes = [
            'name' => $data['name'],
            'amount' => $data['price'],
            'deduction_id' => 1,
            'qty' => $data['value'],
        ];
        $orderDeduction = new PreOrderDeduction($attributes);
        $orderDeduction->checked = $data['checked'];
        $orderDeduction->setOrder($this->event->getOrderModel());
        //$event->addData($data);
    }

    public function subscribe($events)
    {
        $events->listen(
            OnDeductionInfoDisplayEvent::class,
            static::class . '@onDisplay'
        );
        $events->listen(

            OnDeductionPriceCalculatedEvent::class,
            static::class . '@onCalculated'
        );

    }
}