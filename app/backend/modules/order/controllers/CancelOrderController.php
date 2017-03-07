<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/3/7
 * Time: 下午3:25
 */

namespace app\backend\modules\order\controllers;


use app\common\components\BaseController;
use app\common\models\Order;
use app\common\models\order\Express;
use app\frontend\modules\order\services\behavior\OrderCancelSend;

class CancelOrderController extends BaseController
{
    public function index()
    {
        //更改订单状态
        $db_order_model = Order::find(\YunShop::request()->order_id);
        $order_cancel = new OrderCancelSend($db_order_model);
        if (!$order_cancel->cancelSendable()) {
            echo '错误';exit;
        }
        $order_cancel->cancelSend();
        //删除快递信息
        $db_express_model = Express::select()->where('order_id', '=', \YunShop::request()->order_id)->first();
        $db_express_model->delete($db_express_model->id);
        header('Location:http://yz.com/'. $this->createWebUrl('order.detail', array('id' => \YunShop::request()->id)));
    }
}