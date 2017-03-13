<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/3
 * Time: 下午2:30
 */

namespace app\frontend\modules\order\controllers;

use app\common\components\BaseController;
use app\common\models\Order;
use app\frontend\modules\order\services\behavior\OrderCancelPay;
use app\frontend\modules\order\services\behavior\OrderCancelSend;
use app\frontend\modules\order\services\behavior\OrderDelete;
use app\frontend\modules\order\services\behavior\OrderPay;
use app\frontend\modules\order\services\behavior\OrderSend;

class OperationController extends BaseController
{
    public function pay(){
        if (\YunShop::app()->ispost) {
            $order = Order::find(\YunShop::request()->order_id);
            $order_pay = new OrderPay($order);
            if (!$order_pay->payable()) {
                return show_json(-1,'状态不正确');
            }
            $order_pay->pay();
            return show_json(1);
        }
    }
    public function cancelPay(){
        $order = Order::find(\YunShop::request()->order_id);
        $cancel_pay = new OrderCancelPay($order);
        if (!$cancel_pay->cancelable()) {
            show_json(-1,'状态不正确');
        }
        $cancel_pay->cancelPay();
        show_json(1);
    }
    public function send(){
        $order = Order::find(\YunShop::request()->order_id);
        $order_send = new OrderSend($order);
        if (!$order_send) {
            show_json(-1,'状态不正确');
        }
        $order_send->send();
        show_json(1);
    }
    public function cancelSend(){
        $order = Order::find(\YunShop::request()->order_id);
        $cancel_send = new OrderCancelSend($order);
        if (!$cancel_send->sendable()) {
            show_json(-1,'状态不正确');
        }
        $cancel_send->cancelSend();
        show_json(1);
    }
    public function Receive(){
        $order = Order::find(\YunShop::request()->order_id);
        $order_receive = new OrderReceive($order);
        if (!$order_receive->receiveable()) {
            show_json(-1,'状态不正确');
        }
        $order_receive->receive();
        show_json(1);
    }
    public function Delete()
    {
        $order = Order::find(\YunShop::request()->order_id);
        $order_delete = new OrderDelete($order);
        if (!$order_delete->deleteable()) {
            show_json(-1,'状态不正确');
        }
        $order_delete->delete();
        show_json(1);
    }
}