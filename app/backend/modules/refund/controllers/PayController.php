<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/21
 * Time: 下午6:16
 */

namespace app\backend\modules\refund\controllers;

use app\backend\modules\refund\models\RefundApply;
use app\backend\modules\refund\services\RefundOperationService;
use app\common\components\BaseController;
use app\common\exceptions\AdminException;
use app\common\models\finance\Balance;
use app\common\models\PayType;
use app\common\services\PayFactory;
use app\frontend\modules\finance\services\BalanceService;
use Illuminate\Support\Facades\DB;

class PayController extends BaseController
{
    private $refundApply;
    /**
     * 退款
     * @param \Request $request
     * @return mixed
     * @throws AdminException
     */
    public function index(\Request $request)
    {
        $this->validate($request, [
            'refund_id' => 'required'
        ]);

        /**
         * @var $this->refundApply RefundApply
         */
        $this->refundApply = RefundApply::find($request->input('refund_id'));
        if (!isset($this->refundApply)) {
            throw new AdminException('未找到退款记录');
        }
        if($this->refundApply->refund_type == RefundApply::REFUND_TYPE_MONEY){
            if ($this->refundApply->status != RefundApply::WAIT_CHECK) {
                throw new AdminException($this->refundApply->status_name . '的退款申请,无法执行' . '打款' . '操作');
            }
        }else{
            if ($this->refundApply->status != RefundApply::WAIT_REFUND) {
                throw new AdminException($this->refundApply->status_name . '的退款申请,无法执行' . '打款' . '操作');
            }
        }

        if(!is_numeric($this->refundApply->order->pay_type_id)){
            throw new AdminException($this->refundApply->id . '获取支付方式失败');

        }

        switch ($this->refundApply->order->pay_type_id){
            case PayType::WECHAT_PAY:
                $this->wechat();
                break;

            case PayType::ALIPAY:
                $this->alipay();
                break;

            case PayType::CREDIT:
                $this->balance();
                break;

            default:
                break;
        }
        return $this->message('操作成功');

    }

    private function wechat()
    {
        $pay = PayFactory::create($this->refundApply->order->pay_type_id);

        $result = $pay->doRefund($this->refundApply->order->order_sn, $this->refundApply->order->price, $this->refundApply->order->price);
        if(!$result){
            return $this->error('微信退款失败');
        }
        if ($this->refundApply->order->pay_type_id == PayType::WECHAT_PAY) {
            RefundOperationService::refundComplete(['order_id', $this->refundApply->order->id]);

        }
    }

    private function alipay()
    {
        $pay = PayFactory::create($this->refundApply->order->pay_type_id);

        $result = $pay->doRefund($this->refundApply->order->order_sn, $this->refundApply->order->price, $this->refundApply->order->price);
        if(!$result){
            return $this->error('支付宝退款失败');
        }
    }


    private function balance()
    {
        $refundApply = $this->refundApply;
        $result = DB::transaction(function () use($refundApply){

            RefundOperationService::refundComplete(['order_id'=> $refundApply->order->id]);

            $data = array(
                'serial_number' => $refundApply->refund_sn,
                'money' => $refundApply->price,
                'remark' => '订单(ID'.$refundApply->order->id.')余额支付退款(ID'.$refundApply->id.')' . $refundApply->price,
                'service_type' => Balance::BALANCE_CANCEL_CONSUME,
                'operator' => Balance::OPERATOR_ORDER_,
                'operator_id' => $refundApply->uid,
                'member_id' => $refundApply->uid
            );
            return (new BalanceService())->balanceChange($data);

        });

        if($result !== true){
            return $this->error($result);
        }

    }
}