<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/25
 * Time: 上午11:00
 */

namespace app\frontend\modules\orderPay\controllers;

use app\common\components\ApiController;
use app\common\exceptions\AppException;
use app\frontend\models\Order;
use app\frontend\models\OrderPay;

class CODController extends ApiController
{
    /**
     * @var Order
     */
    private $orderPay;

    /**
     * CODController constructor.
     * @throws AppException
     */
    public function __construct()
    {
        parent::__construct();
        $orderPayId = request()->input('order_pay_id');
        $this->orderPay = OrderPay::find($orderPayId);
        if (!isset($this->orderPay)) {
            throw new AppException('(ID' . request()->input('order_pay_id') . ')支付流水记录不存在');
        }

    }

    /**
     * @param $request
     */
    public function index($request)
    {
        dd($this->orderPay);
        exit;

    }

    /**
     * @throws AppException
     */
    private function valid()
    {
        if (\Setting::get('shop.pay.COD') == false) {
            throw new AppException('商城未开启货到付款');
        }
    }

}