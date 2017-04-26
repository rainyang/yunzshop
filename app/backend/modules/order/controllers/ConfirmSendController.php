<?php
/**
 * 发货
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/3/7
 * Time: 上午10:18
 */

namespace app\backend\modules\order\controllers;


use app\common\components\BaseController;
use app\common\models\Order;
use app\common\models\order\Express;
use app\frontend\modules\order\services\behavior\Send;

class ConfirmSendController extends BaseController
{
    public function index()
    {
        $order = Order::find(\YunShop::request()->order_id);
        $order_send = new Send($order);
        if (!$order_send->sendable()) {
            $this->message('该订单不能发货');
        }
        $order_send->send();
        $this->message('发货成功', $this->createWebUrl('order.detail', array('id' => \YunShop::request()->id)), 'success');
    }
}