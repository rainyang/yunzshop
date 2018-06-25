<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/25
 * Time: 下午5:34
 */

namespace app\frontend\modules\remittance\controllers;

use app\common\components\ApiController;
use app\common\exceptions\AppException;
use app\frontend\models\Order;
use app\frontend\models\RemittanceRecord;

class RemittanceRecordController extends ApiController
{
    /**
     * @throws AppException
     */
    public function index()
    {
        $orderId = request()->input('order_id');
        $order = Order::find($orderId);
        if(!isset($order)){
            throw new AppException("我找到id为{$orderId}的订单记录");
        }
        /**
         * @var RemittanceRecord $remittanceRecord
         */
        $remittanceRecord = RemittanceRecord::where('order_pay_id',$order->order_pay_id)->first();
        if(!isset($remittanceRecord)){
            throw new AppException("我找到order_pay_id为{$order->order_pay_id}的转账记录");
        }
        $remittanceRecord->status_name = $remittanceRecord->currentProcess()->status_name;
        return $this->successJson($remittanceRecord);

    }
}