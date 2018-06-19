<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/11
 * Time: 下午2:31
 */
namespace app\backend\modules\orderPay\controllers;

use app\backend\modules\order\models\OrderPay;
use app\common\components\BaseController;

class DetailController extends BaseController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function index()
    {
        $orderPayId = request()->query('order_pay_id');
        $orderPays = OrderPay::with('orders')->find($orderPayId);

        return view('orderPay.detail', [
            'orderPays' => json_encode($orderPays)
        ])->render();
    }
}