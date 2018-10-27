<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/12
 * Time: 上午11:17
 */

namespace app\frontend\modules\finance\services;


use app\common\events\order\AfterOrderCreatedEvent;
use app\common\facades\Setting;
use app\common\models\Order;
use app\common\services\finance\PointService;

class AfterOrderDeductiblePointService
{
    private $order_model;
    private $point_set;
    private $preGenerateOrder;

    public static function isChecked($deduction_ids, $id = 1)
    {
        if (!is_array($deduction_ids)) {
            $deduction_ids = json_decode($deduction_ids, true);
            if (!is_array($deduction_ids)) {
                $deduction_ids = explode(',', $deduction_ids);
            }
        }
        return in_array($id, $deduction_ids);
    }

    public function deductiblePoint(AfterOrderCreatedEvent $event)
    {
        $this->order_model = Order::find($event->getOrderModel()->id);
        $this->preGenerateOrder = $event->getOrderModel();
        $this->point_set = Setting::get('point.set');
        $this->calculationPoint();
    }

    private function calculationPoint()
    {
        if (!$this->isDeductible()) {
            return;
        }
        $this->addPointLog();
    }

    private function isDeductible()
    {
        $deduction_ids = $this->preGenerateOrder->getParams('deduction_ids');

        if (!self::isChecked($deduction_ids)) {
            return false;
        }
        return true;
    }

    private function getPointData()
    {
        $point_service = new CalculationPointService($this->order_model, $this->order_model->uid);
        return [
            'point_income_type' => -1,
            'point_mode' => 6,
            'member_id' => $this->order_model->uid,
            'point' => -$point_service->point,
            'remark' => '订单[' . $this->order_model->order_sn . ']抵扣[' . $point_service->point_money . ']元'
        ];
    }

    private function addPointLog()
    {
        $point_service = new PointService($this->getPointData());
        $point_service->changePoint();
    }
}