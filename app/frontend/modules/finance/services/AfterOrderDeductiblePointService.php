<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/4/12
 * Time: 上午11:17
 */

namespace app\frontend\modules\finance\services;


use app\common\events\order\AfterOrderCreatedEvent;
use app\common\models\Order;
use app\common\services\finance\PointService;
use Setting;

class AfterOrderDeductiblePointService
{
    private $order_model;
    private $point_set;

    private function isChecked($id = 1)
    {
        $deduction_ids = \Request::input('deduction_ids');
        if (!is_array($deduction_ids)) {
            $deduction_ids = json_decode($deduction_ids,true);
            if (!is_array($deduction_ids)) {
                $deduction_ids = explode(',',$deduction_ids);
            }
        }
        return in_array($id,$deduction_ids);
    }

    public function deductiblePoint(AfterOrderCreatedEvent $event)
    {
        $this->order_model = Order::find($event->getOrderModel()->id);
        $this->point_set = Setting::get('point.set');
        $this->calculationPoint();
    }

    private function calculationPoint()
    {
        $this->isDeductible();
        $this->addPointLog();
    }

    private function isDeductible()
    {
        if (!$this->isChecked()) {
            return;
        }
    }

    private function getPointData()
    {
        if (!$this->point_set['money']) {
            return [];
        }
        return [
            'point_income_type' => -1,
            'point_mode'        => 6,
            'member_id'         => $this->order_model->uid,
            'point'             => $this->order_model->point_money / $this->point_set['money'],
            'remark'            => '订单[' . $this->order_model->order_sn . ']抵扣[' . $this->order_model->point_money .  ']元'
        ];
    }

    private function addPointLog()
    {
        $point_service = new PointService($this->getPointData());
        if ($point_service) {
            //积分抵扣通知
        }
    }
}