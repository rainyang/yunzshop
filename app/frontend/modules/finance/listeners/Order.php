<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/5/2
 * Time: 上午10:59
 */
namespace app\frontend\modules\finance\listeners;

use app\common\events\discount\OnDeductionInfoDisplayEvent;
use app\common\events\discount\OnDeductionPriceCalculatedEvent;
use app\frontend\modules\finance\services\CalculationPointService;

class Order
{
    private $event;

    public function onDisplay(OnDeductionInfoDisplayEvent $event)
    {
        $this->event = $event;

        $data = $this->getPointData();
        if (!$data) {
            return null;
        }
        $event->addData($data);
    }

    private function isChecked($id = 1)
    {
        $deduction_ids = $this->event->getOrderModel()->getParams('deduction_ids');
        if (!is_array($deduction_ids)) {
            $deduction_ids = json_decode($deduction_ids,true);
            if (!is_array($deduction_ids)) {
                $deduction_ids = explode(',',$deduction_ids);
            }
        }
        return in_array($id,$deduction_ids);
    }

    private function getPointData()
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

        if($this->isChecked() == false){
            return null;
        }
        $data = $this->getPointData();
        if (!$data) {
            return null;
        }
        $event->addData($data);
    }

    public function subscribe($events)
    {
        $events->listen(
            OnDeductionInfoDisplayEvent::class,
            self::class . '@onDisplay'
        );
        $events->listen(

            OnDeductionPriceCalculatedEvent::class,
            self::class . '@onCalculated'
        );

    }
}