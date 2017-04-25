<?php
/**
 * 收货
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/3/7
 * Time: 下午4:20
 */

namespace app\backend\modules\order\controllers;


use app\common\components\BaseController;
use app\common\models\Order;
use app\frontend\modules\order\services\behavior\OrderReceive;

class CompleteReceiveController extends BaseController
{
    public function index()
    {
        $db_order_model = Order::find(\YunShop::request()->order_id);
        $complete = new OrderReceive($db_order_model);
        if (!$complete->receiveable()) {
            $this->message('失败');
        }
        $complete->receive();
        $this->message('确认收货成功', $this->createWebUrl('order.detail', array('id' => \YunShop::request()->id)), 'success');
    }
}