<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/3
 * Time: 下午2:30
 */

namespace app\backend\modules\order\controllers;

use app\common\components\BaseController;
use app\common\helpers\Url;
use app\common\models\Order;
use app\common\models\PayType;
use app\frontend\modules\order\services\OrderService;

class OperationController extends BaseController
{
    protected $param;
    protected $order;
    public $transactionActions = ['*'];

    public function __construct()
    {
        parent::__construct();
        $this->param = \Request::input();
        if (!isset($this->param['order_id'])) {
            return $this->message('order_id不能为空!', '', 'error');

        }
        $this->order = Order::find($this->param['order_id']);
        if (!isset($this->order)) {
            return $this->message('未找到该订单!', '', 'error');

        }
    }

    public function pay()
    {
        $this->param['pay_type_id'] = PayType::BACKEND;
        $message = OrderService::orderPay($this->param);
        return $this->successJson($message);

    }

    public function cancelPay()
    {
        $message = OrderService::orderCancelPay($this->param);
        //return $this->message($message,'', 'error');

        return $this->message($message);
    }

    public function send()
    {
        $message = OrderService::orderSend($this->param);

        return $this->message($message);
    }

    public function cancelSend()
    {
        $message = OrderService::orderCancelSend($this->param);

        return $this->message($message);
    }

    public function Receive()
    {
        $message = OrderService::orderReceive($this->param);

        return $this->message($message);
    }

    public function Close()
    {
        $message = OrderService::orderClose($this->param);

        return $this->message($message);
    }

    public function Delete()
    {
        $message = OrderService::orderDelete($this->param);

        return $this->message($message);
    }
}