<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/3
 * Time: 下午2:30
 */

namespace app\backend\modules\order\controllers;

use app\common\components\BaseController;
use app\common\helpers\Url;
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
        if (!isset($this->_params['order_id'])) {
            return $this->message('order_id不能为空!','', 'error');

        }
        $this->_Order = Order::find($this->_params['order_id']);
        if (!isset($this->_Order)) {
            return $this->message('未找到该订单!','', 'error');

        }
    }

    public function pay()
    {
        list($result, $message) = OrderService::orderPay($this->_params);
        if ($result === false) {
            return $this->message($message,'', 'error');

        }
        return $this->message($message);

    }

    public function cancelPay()
    {
        list($result, $message) = OrderService::orderCancelPay($this->_params);
        if ($result === false) {
            return $this->message($message,'', 'error');

        }
        return $this->message($message);
    }

    public function send()
    {
        list($result, $data) = OrderService::orderSend($this->_params);
        if ($result === false) {
            return $this->message($data,'', 'error');
        }
        return $this->message($data);
    }

    public function cancelSend()
    {
        list($result, $data) = OrderService::orderCancelSend($this->_params);
        if ($result === false) {
            return $this->message($data,'', 'error');
        }
        return $this->message($data);
    }

    public function Receive()
    {
        list($result, $data) = OrderService::orderReceive($this->_params);
        if ($result === false) {
            return $this->message($data,'', 'error');
        }
        return $this->message($data);
    }

    public function Delete()
    {
        list($result, $data) = OrderService::orderDelete($this->_params);
        if ($result === false) {
            return $this->message($data,'', 'error');
        }
        return $this->message($data);
    }
}