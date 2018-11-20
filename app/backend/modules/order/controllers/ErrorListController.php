<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/11/16
 * Time: 3:39 PM
 */

namespace app\backend\modules\order\controllers;


use app\common\models\Order;
use Illuminate\Support\Facades\DB;

class ErrorListController extends ListController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function pointFix()
    {
        $this->orderModel->select($this->orderModel->getModel()->getTable() . '.*')->
        where('create_time', '>', strtotime("2018-10-25"))->join('yz_order_deduction', function ($query) {
            $query->on('yz_order_deduction.order_id', 'yz_order.id')
                ->on('yz_order_deduction.amount', '>', 'yz_order.deduction_price')
                ->where('yz_order_deduction.amount', '>', 0)->where('code', 'point');

        })->where('status', -1);
        return view('order.index', $this->getData())->render();
    }

    /**
     * @return string
     * @throws \Throwable
     */
    public function errorPoint()
    {
        $this->orderModel->whereIn('order_sn', explode(',','SN20181026141637GP,SN20181026141840Rz,SN20181026142743XX,SN20181026143007AT,SN20181026154345AT,SN20181026203108FQ,SN20181026203129KM,SN20181027135712Vj'));
        return view('order.index', $this->getData())->render();
    }

    /**
     * @return string
     * @throws \Throwable
     */
    public function callbackFail()
    {
        $orderIds = DB::table('yz_order as o')->join('yz_order_pay_order as opo', 'o.id', '=', 'opo.order_id')
            ->join('yz_order_pay as op', 'op.id', '=', 'opo.order_pay_id')
            ->join('yz_pay_order as po', 'po.out_order_no', '=', 'op.pay_sn')
            ->whereIn('o.status', [0, -1])
            ->where('op.status', 0)
            ->where('po.status', 2)
            ->distinct()->pluck('o.id');
        $this->orderModel = Order::orders(request('search'))->whereIn('id', $orderIds);
        return view('order.index', $this->getData())->render();

    }

    /**
     * @return string
     * @throws \Throwable
     */
    public function payFail()
    {
        $orderIds = DB::table('yz_order as o')->join('yz_order_pay_order as opo', 'o.id', '=', 'opo.order_id')
            ->join('yz_order_pay as op', 'op.id', '=', 'opo.order_pay_id')
            ->whereIn('o.status', [0, -1])
            ->where('op.status', 1)
            ->pluck('o.id');
        $this->orderModel = Order::orders(request('search'))->whereIn('id', $orderIds);
        return view('order.index', $this->getData())->render();

    }
}