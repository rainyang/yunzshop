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
        $paymentTypes = app('PaymentManager')->make('OrderPaymentTypeManager')->getOrderPaymentTypes();

        $buttons =  $paymentTypes->map(function (BasePayment $paymentType) {
            return [
                'name' => $paymentType->getName(),
                'value' => $paymentType->getId(),
                'need_password' => $paymentType->needPassword(),
            ];
        });

        $data = [ 'buttons' => $buttons];

        return $this->successJson('成功', $data);
    }
}