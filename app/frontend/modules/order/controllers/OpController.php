<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/3
 * Time: 下午2:30
 */

namespace app\frontend\modules\order\controllers;

use app\common\models\Order;
use app\frontend\modules\order\services\behavior\OrderCancelPay;

class OpController
{
    public function pay(){

    }
    public function cancelPay(){
        $order = Order::first();
        $cancel_pay = new OrderCancelPay($order);
        if(!$cancel_pay->cancelable()){
            echo '状态不正确';exit;
        }
        $cancel_pay->cancelPay();
    }
    public function send(){

    }
    public function cancelSend(){

    }
    public function Receive(){

    }
}