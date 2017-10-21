<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/9/1 下午4:33
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\common\services\finance;


use app\common\facades\Setting;

class PointRollbackService
{
    private $orderModel;

    public function orderCancel($event)
    {
        if (!Setting::get('point.set.point_rollback')) {
            return;
        }
        $this->orderModel = $event->getOrderModel();
        $pointDeduction = $this->getOrderPointDeduction($this->orderModel->deductions);
        if (!$pointDeduction) {
            return;
        }
        return $this->pointRollback($pointDeduction);
    }

    private function getOrderPointDeduction($orderDeductions)
    {
        $point = 0;
        if ($orderDeductions) {
            foreach ($orderDeductions as $key => $deduction) {
                if ($deduction['code'] == 'point') {
                    $point = $deduction['coin'];
                    break;
                }
            }
        }
        return $point;
    }

    private function pointRollback($point)
    {
        return (new PointService($this->getChangeData($point)))->changePoint();
    }

    private function getChangeData($point)
    {
        return [
            'point_income_type' => PointService::POINT_INCOME_GET,
            'point_mode'        => PointService::POINT_MODE_ROLLBACK,
            'member_id'         => $this->orderModel->uid,
            'point'             => $point,
            'remark'            => '订单：'.$this->orderModel->order_sn.'关闭，返还积分抵扣积分'.$point,
        ];
    }

}
