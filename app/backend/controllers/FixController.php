<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/8/8
 * Time: 下午6:52
 */

namespace app\backend\controllers;


use app\common\components\BaseController;
use app\common\models\Order;
use Yunshop\Commission\models\CommissionOrder;

class FixController extends BaseController
{
    public function handleCommissionOrder()
    {

        $handle = 0;
        $success = 0;

        $waitCommissionOrder = CommissionOrder::uniacid()->whereStatus(0)->get();



        if (!$waitCommissionOrder->isEmpty()) {

            foreach ($waitCommissionOrder as $key => $commissionOrder) {

                $orderModel = Order::uniacid()->whereId($commissionOrder->ordertable_id)->first();

                if ($orderModel->status == 3) {

                    $handle += 1;
                    $commissionOrder->status = 1;

                    if ($commissionOrder->save()) {
                        $success += 1;
                    }
                }
                unset($orderModel);
            }
        }

        echo "分销订单未结算总数：{$waitCommissionOrder->count()}，已完成订单数：{$handle}, 执行成功数：{$success}";
    }
}