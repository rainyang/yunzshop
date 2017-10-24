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
    public $transactionActions = ['*'];
    protected $params;
    protected $order;

    public function __construct()
    {

        parent::__construct();
        $this->params = request()->input();
        if (!isset($this->params['order_id'])) {
            return $this->errorJson('order_id 不能为空!');
        }
        $this->order = app('OrderManager')->make('Order')->find($this->params['order_id']);
        if (!isset($this->order)) {
            return $this->errorJson('未找到该订单!');
        }
    }

    public function pay()
    {
        $message = OrderService::orderPay($this->params);

        return $this->successJson($message);

    }

    public function cancelPay()
    {
        $message = OrderService::orderCancelPay($this->params);
        return $this->successJson($message);
    }

    public function send()
    {
        $message = OrderService::orderSend($this->params);

        return $this->successJson($message);
    }

    public function cancelSend()
    {
        $message = OrderService::orderCancelSend($this->params);
        return $this->successJson($message);
    }

    public function Receive()
    {
        $message = OrderService::orderReceive($this->params);
        return $this->successJson($message);
    }

    public function Delete()
    {
        $message = OrderService::orderDelete($this->params);
        return $this->successJson($message);
    }

    public function Close()
    {
        $message = OrderService::orderClose($this->params);
        return $this->successJson($message);
    }
}