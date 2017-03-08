<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/3/7
 * Time: 上午10:18
 */

namespace app\backend\modules\order\controllers;


use app\common\components\BaseController;
use app\common\models\Order;
use app\common\models\order\Express;
use app\frontend\modules\order\services\behavior\OrderSend;

class ConfirmSendController extends BaseController
{
    public function index()
    {
        $db_express_model = new Express();
        $db_express_model->order_id = \YunShop::request()->order_id;
        $db_express_model->express_code = \YunShop::request()->express_code;
        $db_express_model->express_company_name = \YunShop::request()->express_company_name;
        $db_express_model->express_sn = \YunShop::request()->express_sn;
        $db_express_model->save();

        $order = Order::find(\YunShop::request()->order_id);
        $order_send = new OrderSend($order);
        if (!$order_send->sendable()) {
            echo '状态不正确';exit;
        }
        $order_send->send();
        header('Location:http://yz.com/'. $this->createWebUrl('order.detail', array('id' => \YunShop::request()->id)));
        dd(1);
    }
}