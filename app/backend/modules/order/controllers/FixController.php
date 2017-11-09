<?php

namespace app\backend\modules\order\controllers;

use app\common\components\BaseController;
use app\common\models\Order;
use app\common\models\OrderGoods;
use app\common\models\OrderPay;
use app\common\models\PayOrder;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 21/02/2017
 * Time: 11:34
 */
class FixController extends BaseController
{
    public function fixOrderPayId(){

        $r = Order::where('pay_time','>',0)->where(function ($query){
            return $query->wherePayTypeId(0)->orWhere('order_pay_id',0);
        })->get();
        $r->each(function($order){

            $orderPay = OrderPay::where(['order_ids'=>'["'.$order->id.'"]'])->orderBy('id','desc')->first();

            if(isset($orderPay)){
                $order->pay_type_id = $orderPay->pay_type_id;
                $order->order_pay_id = $orderPay->id;
                $order->save();
            }

        });
        echo 1;
        exit;

    }
    public function time()
    {
        Order::whereIn('status', [0, 1, 2, 3])->where('create_time', 0)->update(['create_time' => time()]);
        Order::whereIn('status', [1, 2, 3])->where('pay_time', 0)->update(['pay_time' => time()]);
        Order::whereIn('status', [2, 3])->where('send_time', 0)->update(['send_time' => time()]);
        Order::whereIn('status', [3])->where('finish_time', 0)->update(['finish_time' => time()]);
        Order::where('status', '-1')->where('cancel_time', 0)->update(['cancel_time' => time()]);
        echo 'ok';

    }

    public function deleteInvalidOrders()
    {
        Order::doesntHave('hasManyOrderGoods')->delete();
        Order::where('goods_price', '<=', 0)->delete();
        OrderGoods::where('goods_price', '<=', 0)->delete();
        echo 'ok';

    }

    public function payType()
    {
        Order::whereIn('status', [1, 2, 3])->where('pay_type_id', 0)->update(['pay_type_id' => 1]);
        echo 'ok';

    }

    public function dispatchType()
    {
        Order::whereIn('status', [2, 3])->where('dispatch_type_id', 0)->update(['dispatch_type_id' => 1]);
        echo 'ok';

    }

    public function index()
    {
        $payOrders = PayOrder::where('updated_at', '>', 0)->get();

        $payOrders->each(function ($payOrder) {
            $orderPay = OrderPay::wherePaySn($payOrder->out_order_no)->first();
            $orders = Order::whereIn('id', $orderPay->order_ids)->get();

            $orders->each(function ($order) use ($payOrder) {
                if ($order->pay_type_id == 0 && $order->status > 0) {
                    if ($payOrder->third_type == '余额') {
                        $order->pay_type_id = 3;
                    } elseif ($payOrder->third_type == '支付宝') {
                        $order->pay_type_id = 2;
                    } elseif ($payOrder->third_type == '微信') {
                        $order->pay_type_id = 1;
                    }
                    $order->save();
                }
            });
        });

    }
}