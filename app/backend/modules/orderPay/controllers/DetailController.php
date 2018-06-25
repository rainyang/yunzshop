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
use Illuminate\Database\Eloquent\Builder;

class DetailController extends BaseController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function index()
    {
        $orderPayId = request()->query('order_pay_id');
        $orderPay = OrderPay::with(['orders'=> function (Builder $query) {
            $query->with('orderGoods');
        },'process','member','payOrder'])->find($orderPayId);


        return view('orderPay.detail', [
            'orderPay' => json_encode($orderPay)
        ])->render();
    }
}