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
        //if (\YunShop::app()->isajax) {
            $set = Setting::get('pay');
            $order_id = \YunShop::request()->order_id = 1;
            VerifyPayService::verifyPay($order_id);
            //还需要商品规格
            $db_order_model = Order::with(
                [
                    'hasManyOrderGoods.belongsToGood',
                    'hasOnePay'
                ]
            )->find($order_id);
            //dd($db_order_model->hasOnePay->pay_sn);
            //member  等会员给方法
            $member = [
                'id'    => '12'
            ];
            VerifyPayService::verifyLog($db_order_model, $member);
            //所有支付方式
            $all_pays = [];
            $return_url = urlencode($this->createMobileUrl('order.pay.display', array('order_id' => $order_id)));

            return show_json(1, [
                'order'     => $db_order_model,
                'set'       => $set,
                'all_pays'  => $all_pays,
                'return_url'    => $return_url,
            ]);
        //}
    }
}