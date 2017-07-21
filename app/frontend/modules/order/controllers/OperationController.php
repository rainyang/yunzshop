<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/3
 * Time: 下午2:30
 */

namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;
use app\common\models\Order;
use app\frontend\modules\order\services\OrderService;

class OperationController extends ApiController
{
    private $_params;
    private $_Order;

    public function __construct()
    {
        parent::__construct();
        $this->_params = \YunShop::request()->get();
        if (!isset($this->_params['order_id'])) {
            return $this->errorJson('order_id 不能为空!');
        }
        $this->_Order = Order::find($this->_params['order_id']);
        if (!isset($this->_Order)) {
            return $this->errorJson('未找到该订单!');
        }
    }

    public function pay()
    {
        $message = OrderService::orderPay($this->_params);

        return $this->successJson($message);

    }

    public function cancelPay()
    {
        $message = OrderService::orderCancelPay($this->_params);
        return $this->successJson($message);
    }

    public function send()
    {
        $message = OrderService::orderSend($this->_params);

        return $this->successJson($message);
    }

    public function cancelSend()
    {
        $message = OrderService::orderCancelSend($this->_params);
        return $this->successJson($message);
    }

    public function Receive()
    {
        $message = OrderService::orderReceive($this->_params);
        return $this->successJson($message);
    }

    public function Delete()
    {
        $message = OrderService::orderDelete($this->_params);
        return $this->successJson($message);
    }

    public function Close()
    {
        $message = OrderService::orderClose($this->_params);
        return $this->successJson($message);
    }
}