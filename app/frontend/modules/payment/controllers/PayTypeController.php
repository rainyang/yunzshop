<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/16
 * Time: 下午3:13
 */

namespace app\frontend\modules\payment\controllers;


use app\common\components\BaseController;
use app\frontend\modules\payment\orderPayments\BasePayment;

class PayTypeController extends BaseController
{
    public function index()
    {
        $buttons = [];
        
        $paymentTypes = app('PaymentManager')->make('OrderPaymentTypeManager')->getOrderPaymentTypes();

         $paymentTypes->map(function (BasePayment $paymentType) {
            return [
                'name' => $paymentType->getName(),
                'value' => $paymentType->getId(),
                'need_password' => $paymentType->needPassword(),
            ];
        })->each(function($item, $key) use (&$buttons) {
            if ($item['value'] != 14) {
                $buttons[] = $item;
            }
        });

        $data = ['buttons' => $buttons];

        return $this->successJson('成功', $data);
    }
}