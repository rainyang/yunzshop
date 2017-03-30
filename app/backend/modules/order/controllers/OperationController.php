<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/3
 * Time: 下午2:30
 */

namespace app\backend\modules\order\controllers;

use app\common\components\BaseController;
use app\common\models\Order;
use app\frontend\modules\order\services\OrderService;

class OperationController extends BaseController
{
    private $_params;
    private $_Order;

    public function __construct()
    {
        parent::__construct();
        $this->_params = \YunShop::request()->get();
        if(!isset($this->_params['order_id'])){
            $this->errorJson('order_id 不能为空!');
            exit;
        }
        $this->_Order = Order::find($this->_params['order_id']);
        if(!isset($this->_Order)){
            $this->errorJson('未找到该订单!');
            exit;
        }
    }

    public function pay(){
        list($result,$message) = OrderService::orderPay($this->_params);
        if($result === false){
            return $this->errorJson($message);
        }
        return $this->successJson($message);

    }
    public function cancelPay(){
        list($result,$message) = OrderService::orderCancelPay($this->_params);
        if($result === false){
            return $this->errorJson($message);
        }
        return $this->successJson($message);
    }
    public function send(){
        list($result,$data) = OrderService::orderSend($this->_params);
        if($result === false){
            $this->message('发货成功', $this->createWebUrl('order.detail', array('id' => $this->_params['order_id'])), 'success');
            exit();
        }
        $this->message('发货失败', $this->createWebUrl('order.detail', array('id' => $this->_params['order_id'])), 'error');
    }
    public function cancelSend(){
        list($result,$data) = OrderService::orderCancelSend($this->_params);
        if($result === false){
            $this->message('取消失败', $this->createWebUrl('order.detail', array('id' => $this->_params['order_id'])), 'error');
            exit();
        }
        $this->message('取消成功', $this->createWebUrl('order.detail', array('id' => $this->_params['order_id'])), 'success');
    }
    public function Receive(){
        list($result,$data) = OrderService::orderReceive($this->_params);
        if($result === false){
            $this->message('收货失败', $this->createWebUrl('order.detail', array('id' => $this->_params['order_id'])), 'error');
            exit();
        }
        $this->message('确认收货成功', $this->createWebUrl('order.detail', array('id' => $this->_params['order_id'])), 'success');
    }
    public function Delete()
    {
        list($result,$data) = OrderService::orderDelete($this->_params);
        if($result === false){
            $this->errorJson($data);
        }
        $this->successJson($data);
    }
}