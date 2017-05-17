<?php

namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;
use app\common\components\BaseController;
use app\frontend\modules\order\models\Order;
use app\frontend\modules\order\models\OrderListModel;

class ListController extends ApiController
{
    private $order;

    public function __construct()
    {
        parent::__construct();
        $this->order = Order::orders()->where('status', '<>', '-1');
    }

    private function getData()
    {
        $pageSize = \YunShop::request()->pagesize;
        $pageSize = $pageSize ? $pageSize : 20;
        return $this->order->paginate($pageSize)->toArray();
    }

    /**
     * 所有订单(不包括"已删除"订单)
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->successJson($msg = 'ok', $data = $this->getData());

    }

    /**
     * 待付款订单
     * @return \Illuminate\Http\JsonResponse
     */
    public function waitPay()
    {
        $this->order->waitPay();
        return $this->successJson($msg = 'ok', $data = $this->getData());

    }

    /**
     * 待发货订单
     * @return \Illuminate\Http\JsonResponse
     */
    public function waitSend()
    {
        $this->order->waitSend();
        return $this->successJson($msg = 'ok', $data = $this->getData());

    }

    /**
     * 待收货订单
     * @return \Illuminate\Http\JsonResponse
     */
    public function waitReceive()
    {
        $this->order->waitReceive();

        return $this->successJson($msg = 'ok', $data = $this->getData());
    }

    /**
     * 已完成订单
     * @return \Illuminate\Http\JsonResponse
     */
    public function completed()
    {
        $this->order->completed();

        return $this->successJson($msg = 'ok', $data = $this->getData());
    }
}