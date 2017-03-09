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
        echo \YunShop::app()->uniacid;
        $shop  = Setting::get('shop');
        dd($shop);
        if (\YunShop::app()->isajax) {
            $order_id = \YunShop::request()->order_id = 1;
            VerifyPayService::verifyPay($order_id);
            $db_order_model = Order::with(
                [
                    'hasOnePay'
                ]
            )->find($order_id);
            //dd($db_order_model->hasOnePay->pay_sn);
            //member  等会员给方法
            $member = [];
            VerifyPayService::verifyLog($db_order_model, $member);
        }
        Setting::get('shop.');
    }
}