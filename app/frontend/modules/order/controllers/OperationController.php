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
use app\frontend\modules\order\services\behavior\OrderCancelSend;
use app\frontend\modules\order\services\behavior\OrderDelete;
use app\frontend\modules\order\services\OrderService;

class OperationController extends BaseController
{
    public function pay(){
        $order = Order::find(\YunShop::request()->order_id);
        list($result,$message) = OrderService::orderPay($order);
        if($result === false){
            return $this->errorJson($message);
        }
        return $this->successJson($message);

    }
    public function cancelPay(){
        $order = Order::find(\YunShop::request()->order_id);
        list($result,$message) = OrderService::orderCancelPay($order);
        if($result === false){
            return $this->errorJson($message);
        }
        return $this->successJson($message);
    }
    public function send(){
        $order = Order::find(\YunShop::request()->order_id);
        list($result,$data) = OrderService::orderSend($order);
        if($result === false){
            $this->errorJson($data);
        }
        $this->successJson($data);
    }
    public function cancelSend(){
        $order = Order::find(\YunShop::request()->order_id);
        list($result,$data) = OrderService::orderCancelSend($order);
        if($result === false){
            $this->errorJson($data);
        }
        $this->successJson($data);
    }
    public function Receive(){
        $order = Order::find(\YunShop::request()->order_id);
        list($result,$data) = OrderService::orderReceive($order);
        if($result === false){
            $this->errorJson($data);
        }
        $this->successJson($data);
    }
    public function Delete()
    {
        $order = Order::find(\YunShop::request()->order_id);
        list($result,$data) = OrderService::orderDelete($order);
        if($result === false){
            $this->errorJson($data);
        }
        $this->successJson($data);
    }
}