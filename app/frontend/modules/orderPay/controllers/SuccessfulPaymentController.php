<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/5/15
 * Time: 17:12
 */

namespace app\frontend\modules\orderPay\controllers;


use app\common\components\ApiController;
use app\common\models\OrderPay;
use app\common\models\Order;

class SuccessfulPaymentController extends ApiController
{


    /**
     * 支付跳转页面
     */
    public function paymentJump($outtradeno)
    {
//        $outtradeno = \YunShop::request()->outtradeno;
        $data = [];
        /**
         * 判断是余额还是第三方支付
         */
//        if ($outtradeno){
//            if (is_string($outtradeno)){
//                $orderPay = OrderPay::where('pay_sn', $outtradeno)->first();
//                $orders = Order::whereIn('id', $orderPay->order_ids)->get();
//                dd($orders);
//            }else{
//                //余额
//                $orderPay = OrderPay::find($outtradeno);
//                $result = $orderPay->getPayResult(PayFactory::PAY_CREDIT);
//            }
//        }


        $data['app_links'] = '';
        if (app('plugins')->isEnabled('app-set')) {
            $set = \Setting::get('shop_app.pay');
            $data['app_links'] = $set['app_links'];
        }
    }
}