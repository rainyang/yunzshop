<?php

namespace app\frontend\modules\order\controllers;

use app\common\components\BaseController;
use app\frontend\modules\order\models\OrderListModel;

class ListController extends BaseController
{
    //获取指定状态的订单
    public function getOrders($status = '')
    {
        $memberId = \Yunshop::request()->memberid;
        if (!$memberId) {
            return $this->errorJson( $msg = '缺少访问参数', $data = []);
        }

        $list = OrderListModel::getRequestOrderList($status);

        if ($list) {
            return $this->successJson($data = $list->toArray());
        } else {
            return $this->errorJson($msg = '未找到数据', $data = []);
        }
    }

    //所有订单(不包括"已删除"订单)
    public function index()
    {
        return $this->getOrders();
    }

    //待付款订单
    public function waitPay()
    {
        return $this->getOrders(0); //待付款订单在数据表中的 status 是 0
    }

    //待发货订单
    public function waitSend()
    {
        return $this->getOrders(1); //待发货订单在数据表中的 status 是 1
    }

    //待收货订单
    public function waitReceive()
    {
        return $this->getOrders(2); //待收货订单在数据表中的 status 是 2
    }

    //已完成订单
    public function Completed()
    {
        return $this->getOrders(3); //已完成订单在数据表中的 status 是 3
    }
}