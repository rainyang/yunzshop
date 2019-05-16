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
use app\common\services\finance\CalculationPointService;

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
        if ($outtradeno){
            if (is_string($outtradeno)){
                $orderPay = OrderPay::where('pay_sn', $outtradeno)->first();
                $orders = Order::whereIn('id', $orderPay->order_ids)->with('orderGoods')->get();
//                $orders = Order::whereIn('id', $orderPay->order_ids)->with('orderGoods')->first();
                foreach ($orders as $itme){
                    foreach ($itme->orderGoods as $goods_model){
                        $integral = $this::calcuationPointByGoods($goods_model);
                        $data['integral'] += $integral['point'];  //$this::calcuationPointByGoods($goods_model);
                    }
                }

            }else{
                //余额
                $orderPay = OrderPay::find($outtradeno);
                $result = $orderPay->getPayResult(PayFactory::PAY_CREDIT);
            }
        }


        $data['app_links'] = '';
        if (app('plugins')->isEnabled('app-set')) {
            $set = \Setting::get('shop_app.pay');
            $data['app_links'] = $set['app_links'];
        }
        dd($data);
    }

    public static function calcuationPointByGoods($order_goods_model)
    {
//        dd($order_goods_model->order_id);
        $point_set = \Setting::get('point.set');


        $order = Order::find($order_goods_model->order_id);
        $order_set = $order->orderSettings->where('key', 'point')->first();
        if ($order_set && $order_set->value['set']['give_point']) {
            $point_set['give_point'] = $order_set->value['set']['give_point'] . '%';
        }


        $point_data = [];
        //todo 如果等于0  不赠送积分
        if (isset($order_goods_model->hasOneGoods->hasOneSale) && $order_goods_model->hasOneGoods->hasOneSale->point !== '' && intval($order_goods_model->hasOneGoods->hasOneSale->point) === 0) {
            return $point_data;
        }



        //todo 如果不等于空，按商品设置赠送积分，否则按统一设置赠送积分
        if (isset($order_goods_model->hasOneGoods->hasOneSale) && !empty($order_goods_model->hasOneGoods->hasOneSale->point)) {
            if (strexists($order_goods_model->hasOneGoods->hasOneSale->point, '%')) {
                $point_data['point'] = floatval(str_replace('%', '', $order_goods_model->hasOneGoods->hasOneSale->point) / 100 * $order_goods_model->payment_amount);
            } else {
                $point_data['point'] = $order_goods_model->hasOneGoods->hasOneSale->point * $order_goods_model->total;
            }
            $point_data['remark'] = '购买商品[' . $order_goods_model->hasOneGoods->title .'(比例:'. $order_goods_model->hasOneGoods->hasOneSale->point .')]赠送['.$point_data['point'].']积分！';
        } else if (!empty($point_set['give_point'] && $point_set['give_point'])) {
            if (strexists($point_set['give_point'], '%')) {
                $point_data['point'] = floatval(str_replace('%', '', $point_set['give_point']) / 100 * $order_goods_model->payment_amount);
            } else {
                $point_data['point'] = $point_set['give_point'] * $order_goods_model->total;
            }
            $point_data['remark'] = "购买商品[统一设置(比例:". $point_set['give_point'] .")]赠送[{$point_data['point']}]积分！";
        }
        return $point_data;
    }
}