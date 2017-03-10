<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/3/9
 * Time: 上午9:38
 */

namespace app\frontend\modules\order\controllers;


use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\models\Order;
use app\frontend\modules\order\services\VerifyPayService;

class PayController extends BaseController
{
    public function display()
    {
        $set = Setting::get('pay');
        $order_id = \YunShop::request()->order_id;
        $msg = VerifyPayService::verifyPay($order_id);
        if ($msg) {
            return $this->errorJson($msg, $data = []);
        }
        $db_order_model = Order::with(
            [
                'hasManyOrderGoods.belongsToGood',
                'hasOnePay'
            ]
        )->find($order_id);
        $log_msg = VerifyPayService::verifyLog($db_order_model);
        if ($log_msg) {
           return $this->errorJson($log_msg, $data = []);
        }
        //所有支付方式
        $all_pays = [];
        $return_url = urlencode($this->createMobileUrl('order.pay.display', array('order_id' => $order_id)));

        return $this->successJson(
            $msg = 'ok',
            $data = [
                'order'     => $db_order_model,
                'set'       => $set,
                'all_pays'  => $all_pays,
                'return_url'    => $return_url,
            ]
        );
    }
}